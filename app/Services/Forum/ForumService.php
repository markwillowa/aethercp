<?php

namespace App\Services\Forum;

use App\Models\ForumBoard;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumPostAttachment;
use App\Models\ForumPostReaction;
use App\Models\ForumReport;
use App\Models\ForumTopic;
use App\Models\ForumTopicSubscription;
use App\Models\ForumUserProfile;
use App\Models\User;
use Database\Seeders\ForumSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ForumService
{
    public const ALLOWED_ATTACHMENT_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf', 'txt'];
    public const MAX_ATTACHMENT_SIZE_KB = 5120;
    public const MAX_ATTACHMENTS_PER_POST = 3;

    public function __construct(
        protected ReputationService $reputationService,
        protected CharacterIdentityService $identityService
    ) {}

    public function seedDefaultsIfEmpty(): void
    {
        $this->reputationService->seedDefaultLevelsIfEmpty();

        if (ForumCategory::count() === 0) {
            (new ForumSeeder())->run();
        }
    }

    public function sanitizeContent(string $raw): string
    {
        $clean = trim($raw);
        // Strip null bytes and dangerous tags
        $clean = str_replace("\0", '', $clean);
        $clean = preg_replace('/<\s*(script|iframe|object|embed|style|link|meta|form)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $clean) ?? $clean;
        $clean = preg_replace('/<\s*(script|iframe|object|embed|style|link|meta|form)[^>]*\/?>/is', '', $clean) ?? $clean;
        // Strip remaining HTML tags so stored content is plain text / BBCode-like quotes
        $clean = strip_tags($clean);

        return trim($clean);
    }

    /**
     * Safely render post content to HTML (XSS-proof: escapes everything first, then formats quotes & mentions).
     */
    public static function formatPostHtml(string $content): string
    {
        $escaped = e($content);

        // Format [quote author=Name]...[/quote] or [quote]...[/quote]
        $escaped = preg_replace_callback(
            '/\[quote(?:=&quot;([^&\]]+)&quot;|=([^\]]+))?\](.*?)\[\/quote\]/is',
            function ($m) {
                $author = trim($m[1] ?: ($m[2] ?: 'Quoted Member'));
                $body = trim($m[3]);
                return '<blockquote class="forum-quote my-3 rounded-xl border-l-4 border-cyan-500/70 bg-slate-900/60 px-4 py-3 text-sm">'
                    . '<div class="text-xs font-semibold text-cyan-400 mb-1">' . $author . ' wrote:</div>'
                    . '<div class="text-slate-300 whitespace-pre-line">' . $body . '</div>'
                    . '</blockquote>';
            },
            $escaped
        ) ?? $escaped;

        // Format @username mentions safely
        $escaped = preg_replace(
            '/(^|\s)@([a-zA-Z0-9_]{2,23})\b/',
            '$1<a href="' . url('/forum/members') . '/$2" class="text-cyan-400 hover:underline font-medium">@$2</a>',
            $escaped
        ) ?? $escaped;

        return nl2br($escaped, false);
    }

    public function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'topic';
        $base = Str::limit($base, 180, '');
        $slug = $base;
        $counter = 2;

        while (ForumTopic::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getHomepageData(?User $currentUser = null, ?string $search = null): array
    {
        $this->seedDefaultsIfEmpty();

        $categories = ForumCategory::where('is_active', true)
            ->with(['activeBoards.lastTopic.lastPostUser', 'activeBoards.lastTopic.author'])
            ->orderBy('sort_order')
            ->get();

        $recentQuery = ForumTopic::with(['board', 'author', 'lastPostUser'])
            ->orderByDesc('last_activity_at')
            ->orderByDesc('id');

        if ($search) {
            $recentQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('posts', function ($pq) use ($search) {
                        $pq->where('content', 'like', "%{$search}%");
                    });
            });
        }

        $recentDiscussions = $recentQuery->take(10)->get();

        $trendingDiscussions = ForumTopic::with(['board', 'author'])
            ->where('last_activity_at', '>=', now()->subDays(14))
            ->orderByDesc('replies_count')
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        $announcements = ForumTopic::with(['board', 'author'])
            ->where('is_announcement', true)
            ->latest('created_at')
            ->take(4)
            ->get();

        $activeMembers = ForumUserProfile::with('user')
            ->orderByDesc('reputation_points')
            ->take(5)
            ->get()
            ->filter(fn ($p) => $p->user !== null);

        $stats = [
            'total_topics' => ForumTopic::count(),
            'total_replies' => max(0, ForumPost::where('is_first_post', false)->count()),
            'total_posts' => ForumPost::count(),
            'member_count' => User::count(),
        ];

        $userStats = null;
        if ($currentUser) {
            $profile = ForumUserProfile::forUser($currentUser->id);
            $userStats = [
                'topics_count' => ForumTopic::where('user_id', $currentUser->id)->count(),
                'replies_count' => ForumPost::where('user_id', $currentUser->id)->where('is_first_post', false)->count(),
                'reputation_points' => (int) $profile->reputation_points,
                'reputation_level' => $this->reputationService->getLevelForPoints((int) $profile->reputation_points),
            ];
        }

        // Batch-resolve identities for all visible users
        $usersToResolve = collect();
        foreach ($categories as $cat) {
            foreach ($cat->activeBoards as $board) {
                if ($board->lastTopic?->lastPostUser) {
                    $usersToResolve->push($board->lastTopic->lastPostUser);
                } elseif ($board->lastTopic?->author) {
                    $usersToResolve->push($board->lastTopic->author);
                }
            }
        }
        foreach ($recentDiscussions as $topic) {
            if ($topic->author) {
                $usersToResolve->push($topic->author);
            }
            if ($topic->lastPostUser) {
                $usersToResolve->push($topic->lastPostUser);
            }
        }
        foreach ($activeMembers as $member) {
            if ($member->user) {
                $usersToResolve->push($member->user);
            }
        }
        if ($currentUser) {
            $usersToResolve->push($currentUser);
        }

        $identities = $this->identityService->resolveBatch($usersToResolve, $this->reputationService);

        return compact(
            'categories',
            'recentDiscussions',
            'trendingDiscussions',
            'announcements',
            'activeMembers',
            'stats',
            'userStats',
            'identities',
            'search'
        );
    }

    public function createTopic(User $user, ForumBoard $board, array $data, array $files = []): ForumTopic
    {
        if (!$board->canUserCreateTopic($user)) {
            throw new \RuntimeException('You do not have permission to create topics in this board.');
        }

        $title = trim(strip_tags((string) $data['title']));
        $content = $this->sanitizeContent((string) $data['content']);

        if ($title === '' || $content === '') {
            throw new \InvalidArgumentException('Title and content are required.');
        }

        return DB::transaction(function () use ($user, $board, $title, $content, $files) {
            $now = now();
            $topic = ForumTopic::create([
                'board_id' => $board->id,
                'user_id' => $user->id,
                'title' => $title,
                'slug' => $this->generateUniqueSlug($title),
                'is_announcement' => $board->slug === 'announcements' && $user->isStaff(),
                'views_count' => 0,
                'replies_count' => 0,
                'last_post_user_id' => $user->id,
                'last_activity_at' => $now,
            ]);

            $post = ForumPost::create([
                'topic_id' => $topic->id,
                'user_id' => $user->id,
                'content' => $content,
                'is_first_post' => true,
            ]);

            $topic->update(['last_post_id' => $post->id]);

            $this->storeAttachments($post, $user, $files);

            // Auto-subscribe author
            ForumTopicSubscription::firstOrCreate([
                'topic_id' => $topic->id,
                'user_id' => $user->id,
            ]);

            $this->refreshBoardStats($board->id);

            return $topic;
        });
    }

    public function createReply(User $user, ForumTopic $topic, string $rawContent, array $files = []): ForumPost
    {
        if ($topic->is_locked && !$user->isStaff()) {
            throw new \RuntimeException('This topic is locked.');
        }

        $content = $this->sanitizeContent($rawContent);
        if ($content === '') {
            throw new \InvalidArgumentException('Reply content cannot be empty.');
        }

        return DB::transaction(function () use ($user, $topic, $content, $files) {
            $post = ForumPost::create([
                'topic_id' => $topic->id,
                'user_id' => $user->id,
                'content' => $content,
                'is_first_post' => false,
            ]);

            $this->storeAttachments($post, $user, $files);

            $topic->update([
                'replies_count' => ForumPost::where('topic_id', $topic->id)->where('is_first_post', false)->count(),
                'last_post_id' => $post->id,
                'last_post_user_id' => $user->id,
                'last_activity_at' => $post->created_at,
            ]);

            $this->refreshBoardStats($topic->board_id);

            return $post;
        });
    }

    public function updatePost(User $user, ForumPost $post, string $rawContent, ?string $newTitle = null): ForumPost
    {
        if ($post->user_id !== $user->id && !$user->isStaff()) {
            throw new \RuntimeException('You do not have permission to edit this post.');
        }

        $content = $this->sanitizeContent($rawContent);
        if ($content === '') {
            throw new \InvalidArgumentException('Post content cannot be empty.');
        }

        return DB::transaction(function () use ($user, $post, $content, $newTitle) {
            $post->update([
                'content' => $content,
                'edited_at' => now(),
                'edited_by' => $user->id,
            ]);

            if ($post->is_first_post && $newTitle !== null) {
                $cleanTitle = trim(strip_tags($newTitle));
                if ($cleanTitle !== '') {
                    $post->topic->update(['title' => $cleanTitle]);
                }
            }

            return $post->fresh();
        });
    }

    public function deletePost(User $user, ForumPost $post): bool
    {
        if ($post->user_id !== $user->id && !$user->isStaff()) {
            throw new \RuntimeException('You do not have permission to delete this post.');
        }

        return DB::transaction(function () use ($post) {
            $topic = $post->topic;
            $boardId = $topic->board_id;

            if ($post->is_first_post) {
                // Deleting first post deletes the whole topic
                $this->reputationService->handleContentDeletion($topic->id, null);
                $topic->posts()->delete();
                $topic->delete();
                $this->refreshBoardStats($boardId);
                return true; // topic deleted
            }

            $this->reputationService->handleContentDeletion(null, $post->id);
            $post->delete();

            $latestPost = ForumPost::where('topic_id', $topic->id)->latest('created_at')->first();
            $topic->update([
                'replies_count' => ForumPost::where('topic_id', $topic->id)->where('is_first_post', false)->count(),
                'last_post_id' => $latestPost?->id,
                'last_post_user_id' => $latestPost?->user_id,
                'last_activity_at' => $latestPost?->created_at ?? $topic->created_at,
            ]);

            $this->refreshBoardStats($boardId);
            return false; // single reply deleted
        });
    }

    public function toggleReaction(User $user, ForumPost $post, string $type = 'like'): array
    {
        if ($post->user_id === $user->id) {
            throw new \RuntimeException('You cannot react to your own post.');
        }

        return DB::transaction(function () use ($user, $post, $type) {
            $existing = ForumPostReaction::where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->where('type', $type)
                ->first();

            if ($existing) {
                $existing->delete();
                $post->likes_count = ForumPostReaction::where('post_id', $post->id)->count();
                $post->save();

                $this->reputationService->reverseReactionAward($post->id, $user->id, $type);

                return [
                    'reacted' => false,
                    'likes_count' => $post->likes_count,
                ];
            }

            ForumPostReaction::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'type' => $type,
            ]);

            $post->likes_count = ForumPostReaction::where('post_id', $post->id)->count();
            $post->save();

            $recipient = $post->author;
            if ($recipient) {
                $this->reputationService->awardReactionCycle($recipient, $user, $post->id, $post->topic_id, $type);
            }

            return [
                'reacted' => true,
                'likes_count' => $post->likes_count,
            ];
        });
    }

    public function markAcceptedAnswer(User $actor, ForumTopic $topic, ForumPost $post): void
    {
        if ($topic->user_id !== $actor->id && !$actor->isStaff()) {
            throw new \RuntimeException('Only the topic author or staff can mark an accepted answer.');
        }

        if ($post->topic_id !== $topic->id || $post->is_first_post) {
            throw new \InvalidArgumentException('Invalid post for accepted answer.');
        }

        DB::transaction(function () use ($actor, $topic, $post) {
            // Unmark previous accepted answer if any
            if ($topic->accepted_post_id && $topic->accepted_post_id !== $post->id) {
                ForumPost::where('id', $topic->accepted_post_id)->update(['is_accepted_answer' => false]);
            }

            $post->update(['is_accepted_answer' => true]);
            $topic->update([
                'is_solved' => true,
                'accepted_post_id' => $post->id,
            ]);

            if ($post->author && $post->user_id !== $actor->id) {
                $this->reputationService->award(
                    $post->author,
                    $actor,
                    ReputationService::EVENT_ACCEPTED_ANSWER,
                    "accepted_answer:{$topic->id}:{$post->id}",
                    "Answer accepted in \"{$topic->title}\"",
                    $topic->id,
                    $post->id
                );
            }
        });
    }

    public function approveGuide(User $staff, ForumTopic $topic): void
    {
        if (!$staff->isStaff()) {
            throw new \RuntimeException('Staff permission required.');
        }

        DB::transaction(function () use ($staff, $topic) {
            $topic->update(['is_approved_guide' => true]);

            if ($topic->author) {
                $this->reputationService->award(
                    $topic->author,
                    $staff,
                    ReputationService::EVENT_APPROVED_GUIDE,
                    "approved_guide:{$topic->id}",
                    "Approved useful guide: \"{$topic->title}\"",
                    $topic->id,
                    $topic->firstPost?->id
                );
            }
        });
    }

    public function recognizePost(User $staff, ForumPost $post): void
    {
        if (!$staff->isStaff()) {
            throw new \RuntimeException('Staff permission required.');
        }

        if (!$post->author) {
            return;
        }

        $this->reputationService->award(
            $post->author,
            $staff,
            ReputationService::EVENT_MODERATOR_RECOGNITION,
            "mod_recognition:{$post->id}",
            "Moderator recognition on post #{$post->id}",
            $post->topic_id,
            $post->id
        );
    }

    public function refreshBoardStats(int $boardId): void
    {
        $board = ForumBoard::find($boardId);
        if (!$board) {
            return;
        }

        $topicsCount = ForumTopic::where('board_id', $boardId)->count();
        $postsCount = ForumPost::whereHas('topic', fn ($q) => $q->where('board_id', $boardId))->count();
        $lastTopic = ForumTopic::where('board_id', $boardId)->orderByDesc('last_activity_at')->first();

        $board->update([
            'topics_count' => $topicsCount,
            'posts_count' => $postsCount,
            'last_topic_id' => $lastTopic?->id,
            'last_post_at' => $lastTopic?->last_activity_at,
        ]);
    }

    protected function storeAttachments(ForumPost $post, User $user, array $files): void
    {
        $validFiles = array_filter($files, fn ($f) => $f instanceof UploadedFile && $f->isValid());
        $validFiles = array_slice($validFiles, 0, self::MAX_ATTACHMENTS_PER_POST);

        foreach ($validFiles as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, self::ALLOWED_ATTACHMENT_EXTENSIONS, true)) {
                throw new \InvalidArgumentException("File extension .{$ext} is not allowed.");
            }

            $path = $file->store("forum_attachments/{$post->id}", 'local');
            ForumPostAttachment::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'original_name' => Str::limit($file->getClientOriginalName(), 240),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize() ?: 0,
            ]);
        }
    }
}
