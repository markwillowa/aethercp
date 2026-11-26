<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumBoard;
use App\Models\ForumCategory;
use App\Models\ForumReport;
use App\Models\ReputationAuditLog;
use App\Models\ReputationLevel;
use App\Models\ReputationTransaction;
use App\Models\User;
use App\Services\Forum\ForumService;
use App\Services\Forum\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminForumController extends Controller
{
    public function __construct(
        protected ForumService $forumService,
        protected ReputationService $reputationService
    ) {}

    public function index()
    {
        $this->forumService->seedDefaultsIfEmpty();

        $categories = ForumCategory::with('boards')->orderBy('sort_order')->get();
        $reports = ForumReport::with(['reporter', 'topic', 'post'])
            ->latest('created_at')
            ->paginate(15);

        return view('admin.forum.index', compact('categories', 'reports'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        ForumCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::lower(Str::random(4)),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'message-square',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Forum category created.');
    }

    public function storeBoard(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:forum_categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
            'staff_only' => ['nullable', 'boolean'],
        ]);

        ForumBoard::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::lower(Str::random(4)),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'hash',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
            'allow_topics_roles' => !empty($validated['staff_only']) ? ['admin', 'staff'] : null,
        ]);

        return back()->with('success', 'Forum board created.');
    }

    public function toggleBoardLock(ForumBoard $board)
    {
        $board->update(['is_locked' => !$board->is_locked]);
        return back()->with('success', $board->is_locked ? 'Board locked.' : 'Board unlocked.');
    }

    public function resolveReport(Request $request, ForumReport $report)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:reviewed,dismissed'],
        ]);

        $report->update([
            'status' => $validated['status'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Report marked as ' . $validated['status'] . '.');
    }

    public function reputationSettings()
    {
        $this->reputationService->seedDefaultLevelsIfEmpty();
        $settings = $this->reputationService->getSettings();
        $levels = ReputationLevel::orderBy('min_points')->orderBy('sort_order')->get();

        return view('admin.forum.reputation-settings', compact('settings', 'levels'));
    }

    public function updateReputationSettings(Request $request)
    {
        $validated = $request->validate([
            'points_positive_reaction' => ['required', 'integer', 'min:0', 'max:1000'],
            'points_accepted_answer' => ['required', 'integer', 'min:0', 'max:1000'],
            'points_approved_guide' => ['required', 'integer', 'min:0', 'max:1000'],
            'points_moderator_recognition' => ['required', 'integer', 'min:0', 'max:1000'],
            'enabled_events' => ['nullable', 'array'],
            'enabled_events.*' => ['string'],
            'reversal_policy' => ['required', 'in:reverse_all,keep_earned'],
            'leaderboard_visible' => ['nullable', 'boolean'],
        ]);

        $this->reputationService->updateSettings($validated);

        return back()->with('success', 'Reputation rules and settings updated.');
    }

    public function updateReputationLevels(Request $request)
    {
        $validated = $request->validate([
            'levels' => ['required', 'array', 'min:1'],
            'levels.*.id' => ['required', 'integer', 'exists:reputation_levels,id'],
            'levels.*.name' => ['required', 'string', 'max:80'],
            'levels.*.min_points' => ['required', 'integer', 'min:0'],
            'levels.*.badge_color' => ['required', 'string', 'max:32'],
            'levels.*.badge_icon' => ['required', 'string', 'max:40'],
            'levels.*.badge_style' => ['required', 'in:subtle,solid,outline,glow'],
        ]);

        foreach ($validated['levels'] as $idx => $item) {
            ReputationLevel::where('id', $item['id'])->update([
                'name' => $item['name'],
                'min_points' => $item['min_points'],
                'badge_color' => $item['badge_color'],
                'badge_icon' => $item['badge_icon'],
                'badge_style' => $item['badge_style'],
                'sort_order' => $idx + 1,
            ]);
        }

        Cache::forget('forum.reputation_levels');

        return back()->with('success', 'Reputation levels and badge appearances updated.');
    }

    public function reputationHistory(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $eventType = trim((string) $request->query('event', ''));

        $txQuery = ReputationTransaction::with(['recipient', 'source', 'topic'])
            ->latest('created_at');

        if ($search !== '') {
            $txQuery->whereHas('recipient', fn ($q) => $q->where('username', 'like', "%{$search}%"));
        }

        if ($eventType !== '') {
            $txQuery->where('event_type', $eventType);
        }

        $transactions = $txQuery->paginate(25)->withQueryString();
        $auditLogs = ReputationAuditLog::with(['admin', 'recipient'])
            ->latest('created_at')
            ->take(20)
            ->get();

        $users = User::orderBy('username')->get(['id', 'username', 'role']);

        return view('admin.forum.reputation-history', compact(
            'transactions',
            'auditLogs',
            'users',
            'search',
            'eventType'
        ));
    }

    public function manualAdjust(Request $request)
    {
        $validated = $request->validate([
            'recipient_user_id' => ['required', 'integer', 'exists:users,id'],
            'points_delta' => ['required', 'integer', 'not_in:0', 'min:-10000', 'max:10000'],
            'reason' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $recipient = User::findOrFail($validated['recipient_user_id']);
        $this->reputationService->manualAdjust(
            Auth::user(),
            $recipient,
            (int) $validated['points_delta'],
            $validated['reason']
        );

        return back()->with('success', "Reputation adjusted for {$recipient->username} and recorded in the audit ledger.");
    }
}
