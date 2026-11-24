<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BadgeDefinition;
use App\Models\BadgeTier;
use App\Models\RewardDefinition;
use App\Models\RewardIssuance;
use App\Services\Ranking\BadgeService;
use App\Services\Ranking\RewardService;
use Illuminate\Http\Request;

class AdminRewardController extends Controller
{
    public function __construct(
        protected RewardService $rewardService,
        protected BadgeService $badgeService
    ) {}

    public function index()
    {
        $this->badgeService->ensureDefaultDefinitionsExist();

        $rewards = RewardDefinition::with(['badge', 'tier'])->latest()->get();
        $badges = BadgeDefinition::orderByDesc('priority')->get();
        $tiers = BadgeTier::orderBy('category')->orderBy('tier_order')->orderBy('stars')->get();
        $issuances = RewardIssuance::with(['definition', 'logs'])->latest()->limit(50)->get();

        return view('admin.rewards.index', compact('rewards', 'badges', 'tiers', 'issuances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:level,job,pvp,mvp,guild,badge',
            'trigger_type' => 'required|in:badge,tier',
            'badge_id' => 'nullable|exists:badge_definitions,id',
            'badge_tier_id' => 'nullable|exists:badge_tiers,id',
            'reward_type' => 'required|in:zeny,item,multi_item',
            'zeny_amount' => 'nullable|integer|min:0|max:2147483647',
            'item_id' => 'nullable|integer|min:1',
            'item_amount' => 'nullable|integer|min:1|max:30000',
            'items_raw' => 'nullable|string|max:1000',
            'policy' => 'required|in:one_time,per_season,per_period,repeatable',
            'period_key' => 'nullable|string|max:32',
            'message' => 'nullable|string|max:255',
        ]);

        $items = [];
        if ($validated['reward_type'] === 'item' && !empty($validated['item_id'])) {
            $items[] = [
                'nameid' => (int) $validated['item_id'],
                'amount' => max(1, (int) ($validated['item_amount'] ?? 1)),
            ];
        } elseif ($validated['reward_type'] === 'multi_item' && !empty($validated['items_raw'])) {
            foreach (explode(',', $validated['items_raw']) as $pair) {
                $parts = array_map('trim', explode(':', $pair));
                $nid = (int) ($parts[0] ?? 0);
                $amt = max(1, (int) ($parts[1] ?? 1));
                if ($nid > 0) {
                    $items[] = ['nameid' => $nid, 'amount' => $amt];
                }
            }
        }

        RewardDefinition::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'trigger_type' => $validated['trigger_type'],
            'badge_id' => $validated['trigger_type'] === 'badge' ? ($validated['badge_id'] ?? null) : null,
            'badge_tier_id' => $validated['trigger_type'] === 'tier' ? ($validated['badge_tier_id'] ?? null) : null,
            'reward_type' => $validated['reward_type'],
            'zeny_amount' => (int) ($validated['zeny_amount'] ?? 0),
            'items_json' => $items,
            'policy' => $validated['policy'],
            'period_key' => $validated['period_key'] ?? null,
            'message' => $validated['message'] ?? null,
            'is_enabled' => $request->has('is_enabled'),
        ]);

        ActivityLog::log('admin.reward_create', "Created milestone reward [{$validated['name']}].");

        return back()->with('success', "Milestone reward [{$validated['name']}] created.");
    }

    public function toggle(int $id)
    {
        $reward = RewardDefinition::findOrFail($id);
        $reward->is_enabled = !$reward->is_enabled;
        $reward->save();

        ActivityLog::log('admin.reward_toggle', "Toggled reward [{$reward->name}] to " . ($reward->is_enabled ? 'enabled' : 'disabled') . '.');

        return back()->with('success', "Reward [{$reward->name}] " . ($reward->is_enabled ? 'enabled' : 'disabled') . '.');
    }

    public function retry(int $id)
    {
        $issuance = RewardIssuance::findOrFail($id);
        $ok = $this->rewardService->deliverIssuance($issuance);
        ActivityLog::log('admin.reward_retry', "Retried reward delivery [{$issuance->transaction_ref}]: " . ($ok ? 'delivered' : 'failed') . '.');

        return $ok
            ? back()->with('success', "Reward [{$issuance->transaction_ref}] delivered successfully!")
            : back()->with('error', "Retry failed for [{$issuance->transaction_ref}]: " . ($issuance->fresh()->last_error ?? 'Unknown error'));
    }
}
