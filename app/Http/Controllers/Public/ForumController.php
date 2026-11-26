<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ForumBoard;
use App\Models\ForumPost;
use App\Models\ForumPostAttachment;
use App\Models\ForumPostReaction;
use App\Models\ForumReport;
use App\Models\ForumTopic;
use App\Models\ForumTopicSubscription;
use App\Services\Forum\CharacterIdentityService;
use App\Services\Forum\ForumService;
use App\Services\Forum\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ForumController extends Controller
{
    public function __construct(
        protected ForumService $forumService,
        protected ReputationService $reputationService,
        protected CharacterIdentityService $identityService
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $data = $this->forumService->getHomepageData(Auth::user(), $search !== '' ? $search : null);

        return view('forum.index', $data);
    }

    public function board(Request $request, string $slug)
    {
        $this->forumService->seedDefaultsIfEmpty();

        $board = ForumBoard::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $search = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'latest');
        $filter = $request->query('filter', 'all');

        $query = ForumTopic::with(['author', 'lastPostUser'])
            ->where('board_id', $board->id);

        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%");
        }

        match ($filter) {
            'unanswered' => $query->where('replies_count', 0),
            'solved' => $query->where('is_solved', true),
            'pinned' => $query->where('is_pinned', true),
            default => null,
        };

        $query->orderByDesc('is_pinned');

        match ($sort) {
            'newest' => $query->orderByDesc('created_at'),
            'most_replies' => $query->orderByDesc('replies_count')->orderByDesc('last_activity_at'),
            'most_viewed' => $query->orderByDesc('views_count')->orderByDesc('last_activity_at'),
            default => $query->orderByDesc('last_activity_at')->orderByDesc('id'),
        };

        $topics = $query->paginate(15)->withQueryString();

        $usersToResolve = collect();
        foreach ($topics as $t) {
            if ($t->author) {
                $usersToResolve->push($t->author);
            }
            if ($t->lastPostUser) {
                $usersToResolve->push($t->lastPostUser);
            }
        }
        $identities = $this->identityService->resolveBatch($usersToResolve, $this->reputationService);

        $canCreateTopic = $board->canUserCreateTopic(Auth::user());

        return view('forum.board', compact(
            'board',
            'topics',
            'identities',
            'search',
            'sort',
            'filter',
            'canCreateTopic'
        ));
    }

    public function createTopic(Request $request)
    {
        $this->forumService->seedDefaultsIfEmpty();
        $user = Auth::user();

        $boards = ForumBoard::with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($b) => $b->canUserCreateTopic($user))
            ->values();

        if ($boards->isEmpty()) {
            return redirect()->route('forum.index')
                ->with('error', 'There are currently no boards available for you to create a topic.');
        }

        $selectedBoardSlug = $request->query('board');
        $selectedBoard = $selectedBoardSlug
            ? $boards->firstWhere('slug', $selectedBoardSlug)
            : $boards->first();

        return view('forum.create-topic', compact('boards', 'selectedBoard'));
    }

    public function storeTopic(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'board_id' => ['required', 'integer', 'exists:forum_boards,id'],
            'title' => ['required', 'string', 'min:4', 'max:200'],
            'content' => ['required', 'string', 'min:10', 'max:20000'],
            'attachments' => ['nullable', 'array', 'max:' . ForumService::MAX_ATTACHMENTS_PER_POST],
            'attachments.*' => [
                'file',
                'max:' . ForumService::MAX_ATTACHMENT_SIZE_KB,
                'mimes:' . implode(',', ForumService::ALLOWED_ATTACHMENT_EXTENSIONS),
            ],
        ]);

        $board = ForumBoard::findOrFail($validated['board_id']);

        if (!$board->canUserCreateTopic($user)) {
            abort(403, 'You are not authorized to create topics in this board.');
        }

        $topic = $this->forumService->createTopic(
            $user,
            $board,
            $validated,
            $request->file('attachments') ?? []
        );

        return redirect()->route('forum.topic.show', $topic->slug)
            ->with('success', 'Your discussion topic has been published!');
    }

    public function showTopic(Request $request, string $slug)
    {
        $topic = ForumTopic::with(['board.category', 'author'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count once per session for this topic
        $viewedKey = 'forum_viewed_topic_' . $topic->id;
        if (!$request->session()->has($viewedKey)) {
            $topic->increment('views_count');
            $request->session()->put($viewedKey, true);
        }

        $posts = ForumPost::with(['author', 'editor', 'attachments'])
            ->where('topic_id', $topic->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $usersToResolve = collect([$topic->author]);
        foreach ($posts as $post) {
            if ($post->author) {
                $usersToResolve->push($post->author);
            }
        }
        $identities = $this->identityService->resolveBatch($usersToResolve, $this->reputationService);

        $user = Auth::user();
        $reactedPostIds = [];
        $isSubscribed = false;

        if ($user) {
            $reactedPostIds = ForumPostReaction::where('user_id', $user->id)
                ->whereIn('post_id', $posts->pluck('id'))
                ->pluck('post_id')
                ->all();

            $isSubscribed = ForumTopicSubscription::where('topic_id', $topic->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        return view('forum.topic', compact(
            'topic',
            'posts',
            'identities',
            'reactedPostIds',
            'isSubscribed'
        ));
    }

    public function storeReply(Request $request, string $slug)
    {
        $user = Auth::user();
        $topic = ForumTopic::where('slug', $slug)->firstOrFail();

        if ($topic->is_locked && !$user->isStaff()) {
            return back()->with('error', 'This discussion is locked and no longer accepts replies.');
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:20000'],
            'attachments' => ['nullable', 'array', 'max:' . ForumService::MAX_ATTACHMENTS_PER_POST],
            'attachments.*' => [
                'file',
                'max:' . ForumService::MAX_ATTACHMENT_SIZE_KB,
                'mimes:' . implode(',', ForumService::ALLOWED_ATTACHMENT_EXTENSIONS),
            ],
        ]);

        $post = $this->forumService->createReply(
            $user,
            $topic,
            $validated['content'],
            $request->file('attachments') ?? []
        );

        return redirect()->route('forum.topic.show', ['slug' => $topic->slug, 'page' => $topic->posts()->paginate(15)->lastPage()])
            ->withFragment('post-' . $post->id)
            ->with('success', 'Your reply has been posted.');
    }

    public function editPost(ForumPost $post)
    {
        $user = Auth::user();
        if ($post->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'You do not have permission to edit this post.');
        }

        $post->load('topic.board');

        return view('forum.edit-post', compact('post'));
    }

    public function updatePost(Request $request, ForumPost $post)
    {
        $user = Auth::user();
        if ($post->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'You do not have permission to edit this post.');
        }

        $rules = [
            'content' => ['required', 'string', 'min:2', 'max:20000'],
        ];
        if ($post->is_first_post) {
            $rules['title'] = ['required', 'string', 'min:4', 'max:200'];
        }

        $validated = $request->validate($rules);

        $this->forumService->updatePost(
            $user,
            $post,
            $validated['content'],
            $validated['title'] ?? null
        );

        return redirect()->route('forum.topic.show', $post->topic->slug)
            ->withFragment('post-' . $post->id)
            ->with('success', 'Post updated successfully.');
    }

    public function deletePost(ForumPost $post)
    {
        $user = Auth::user();
        if ($post->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'You do not have permission to delete this post.');
        }

        $boardSlug = $post->topic->board->slug;
        $topicSlug = $post->topic->slug;

        $topicDeleted = $this->forumService->deletePost($user, $post);

        if ($topicDeleted) {
            return redirect()->route('forum.board', $boardSlug)
                ->with('success', 'Topic has been deleted.');
        }

        return redirect()->route('forum.topic.show', $topicSlug)
            ->with('success', 'Reply has been deleted.');
    }

    public function toggleReaction(Request $request, ForumPost $post)
    {
        $user = Auth::user();
        if ($post->user_id === $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You cannot react to your own post.'], 422);
            }
            return back()->with('error', 'You cannot react to your own post.');
        }

        $result = $this->forumService->toggleReaction($user, $post, 'like');

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->withFragment('post-' . $post->id);
    }

    public function acceptAnswer(ForumTopic $topic, ForumPost $post)
    {
        $user = Auth::user();
        if ($topic->user_id !== $user->id && !$user->isStaff()) {
            abort(403, 'Only the topic author or staff can accept an answer.');
        }

        $this->forumService->markAcceptedAnswer($user, $topic, $post);

        return back()->with('success', 'Marked as accepted answer.');
    }

    public function toggleSubscription(ForumTopic $topic)
    {
        $user = Auth::user();
        $existing = ForumTopicSubscription::where('topic_id', $topic->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Unsubscribed from topic.');
        }

        ForumTopicSubscription::create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Subscribed to topic.');
    }

    public function report(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => ['nullable', 'integer', 'exists:forum_topics,id'],
            'post_id' => ['nullable', 'integer', 'exists:forum_posts,id'],
            'reason' => ['required', 'string', 'min:3', 'max:100'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['topic_id']) && empty($validated['post_id'])) {
            return back()->with('error', 'Please specify a topic or post to report.');
        }

        ForumReport::create([
            'reporter_id' => Auth::id(),
            'topic_id' => $validated['topic_id'] ?? null,
            'post_id' => $validated['post_id'] ?? null,
            'reason' => strip_tags($validated['reason']),
            'details' => isset($validated['details']) ? strip_tags($validated['details']) : null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thank you. Your report has been submitted to the moderation team.');
    }

    public function togglePin(ForumTopic $topic)
    {
        if (!Auth::user()->isStaff()) {
            abort(403);
        }
        $topic->update(['is_pinned' => !$topic->is_pinned]);
        return back()->with('success', $topic->is_pinned ? 'Topic pinned.' : 'Topic unpinned.');
    }

    public function toggleLock(ForumTopic $topic)
    {
        if (!Auth::user()->isStaff()) {
            abort(403);
        }
        $topic->update(['is_locked' => !$topic->is_locked]);
        return back()->with('success', $topic->is_locked ? 'Topic locked.' : 'Topic unlocked.');
    }

    public function approveGuide(ForumTopic $topic)
    {
        if (!Auth::user()->isStaff()) {
            abort(403);
        }
        $this->forumService->approveGuide(Auth::user(), $topic);
        return back()->with('success', 'Topic marked as an approved guide.');
    }

    public function recognizePost(ForumPost $post)
    {
        if (!Auth::user()->isStaff()) {
            abort(403);
        }
        $this->forumService->recognizePost(Auth::user(), $post);
        return back()->with('success', 'Moderator recognition awarded.');
    }

    public function downloadAttachment(ForumPostAttachment $attachment)
    {
        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->download($attachment->file_path, $attachment->original_name);
    }
}
