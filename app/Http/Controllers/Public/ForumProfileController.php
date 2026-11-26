<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\ForumUserProfile;
use App\Models\PlayerAchievement;
use App\Models\ReputationTransaction;
use App\Models\User;
use App\Services\Forum\CharacterIdentityService;
use App\Services\Forum\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumProfileController extends Controller
{
    public function __construct(
        protected ReputationService $reputationService,
        protected CharacterIdentityService $identityService
    ) {}

    public function show(Request $request, string $username)
    {
        $member = User::whereRaw('LOWER(username) = LOWER(?)', [$username])->firstOrFail();
        $profile = ForumUserProfile::forUser($member->id);
        $identities = $this->identityService->resolveBatch(collect([$member]), $this->reputationService);
        $identity = $identities[$member->id];

        $tab = $request->query('tab', 'overview');

        $stats = [
            'topics_count' => ForumTopic::where('user_id', $member->id)->count(),
            'replies_count' => ForumPost::where('user_id', $member->id)->where('is_first_post', false)->count(),
            'reactions_received' => (int) ForumPost::where('user_id', $member->id)->sum('likes_count'),
            'reputation_points' => (int) $profile->reputation_points,
        ];

        $discussions = ForumTopic::with('board')
            ->where('user_id', $member->id)
            ->latest('created_at')
            ->paginate(10, ['*'], 'topics_page')
            ->withQueryString();

        $replies = ForumPost::with('topic.board')
            ->where('user_id', $member->id)
            ->where('is_first_post', false)
            ->latest('created_at')
            ->paginate(10, ['*'], 'replies_page')
            ->withQueryString();

        $reputationHistory = ReputationTransaction::with('source')
            ->where('recipient_user_id', $member->id)
            ->latest('created_at')
            ->paginate(15, ['*'], 'rep_page')
            ->withQueryString();

        $achievements = collect();
        if ($member->account_id) {
            try {
                $achievements = PlayerAchievement::where('account_id', $member->account_id)
                    ->latest('unlocked_at')
                    ->take(12)
                    ->get();
            } catch (\Throwable $e) {
                $achievements = collect();
            }
        }

        $levels = $this->reputationService->getLevels();

        return view('forum.profile', compact(
            'member',
            'profile',
            'identity',
            'tab',
            'stats',
            'discussions',
            'replies',
            'reputationHistory',
            'achievements',
            'levels'
        ));
    }

    public function settings()
    {
        $user = Auth::user();
        $profile = ForumUserProfile::forUser($user->id);
        $characters = $this->identityService->getOwnedCharacters($user);
        $identities = $this->identityService->resolveBatch(collect([$user]), $this->reputationService);
        $currentIdentity = $identities[$user->id];

        return view('forum.identity-settings', compact('user', 'profile', 'characters', 'currentIdentity'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'identity_mode' => ['required', 'in:username,character'],
            'selected_char_id' => ['nullable', 'integer'],
        ]);

        try {
            $this->identityService->updateIdentity(
                $user,
                $validated['identity_mode'],
                isset($validated['selected_char_id']) ? (int) $validated['selected_char_id'] : null
            );

            return back()->with('success', 'Your forum character identity preference has been updated.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function leaderboard(Request $request)
    {
        $settings = $this->reputationService->getSettings();
        if (!$settings['leaderboard_visible'] && !Auth::user()?->isStaff()) {
            abort(403, 'The community reputation leaderboard is currently hidden.');
        }

        $period = $request->query('period', 'all');
        if (!in_array($period, ['all', 'month', 'week'], true)) {
            $period = 'all';
        }

        $entries = $this->reputationService->getLeaderboard($period, 20);
        $users = $entries->getCollection()->pluck('user')->filter();
        $identities = $this->identityService->resolveBatch($users, $this->reputationService);
        $levels = $this->reputationService->getLevels();

        return view('forum.leaderboard', compact('entries', 'identities', 'period', 'levels'));
    }
}
