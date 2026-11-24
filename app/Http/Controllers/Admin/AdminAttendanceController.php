<?php

namespace App\Services\Attendance;

// Helper reference
namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\AttendanceCampaign;
use App\Models\AttendanceCampaignDay;
use App\Models\AttendanceCampaignItem;
use App\Models\AttendanceClaim;
use App\Models\AttendanceDelivery;
use App\Models\AttendanceParticipation;
use App\Services\Attendance\AttendanceDeliveryService;
use App\Services\Attendance\AttendanceScheduleBuilderService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAttendanceController extends Controller
{
    public function __construct(
        protected AttendanceScheduleBuilderService $builderService,
        protected AttendanceDeliveryService $deliveryService,
        protected ItemRepositoryInterface $itemRepo
    ) {}

    public function index(Request $request)
    {
        $query = AttendanceCampaign::withCount(['days', 'participations', 'claims']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $campaigns = $query->orderBy('display_order')->orderByDesc('id')->paginate(15);

        // Summary Stats
        $stats = [
            'total_campaigns' => AttendanceCampaign::count(),
            'active_campaigns' => AttendanceCampaign::where('status', 'active')->count(),
            'total_participants' => AttendanceParticipation::count(),
            'total_claims' => AttendanceClaim::count(),
            'delivered_count' => AttendanceDelivery::where('status', 'delivered')->count(),
            'failed_count' => AttendanceDelivery::where('status', 'failed')->count(),
        ];

        return view('admin.attendance.index', compact('campaigns', 'stats'));
    }

    public function create()
    {
        return view('admin.attendance.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'nullable|string|max:120|unique:attendance_campaigns,slug',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:3650',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'timezone' => 'required|string|max:64',
            'status' => 'required|in:draft,scheduled,active,paused,completed',
            'progression_mode' => 'required|in:cumulative,consecutive,scheduled',
            'reset_rule' => 'required|in:none,reset_to_start,freeze_streak',
            'participation_scope' => 'required|in:account,character',
            'delivery_destination' => 'required|in:inbox,storage,character',
            'allow_character_choice' => 'boolean',
            'require_in_game_login' => 'boolean',
            'repeatable' => 'boolean',
            'display_order' => 'integer',
            'min_base_level' => 'integer|min:1',
            'min_account_age_days' => 'integer|min:0',
            'daily_reset_hour' => 'integer|min:0|max:23',
            'auto_generate_schedule' => 'nullable|boolean',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['allow_character_choice'] = $request->has('allow_character_choice');
        $validated['require_in_game_login'] = $request->has('require_in_game_login');
        $validated['repeatable'] = $request->has('repeatable');

        $campaign = AttendanceCampaign::create($validated);

        // If admin requested auto-generating baseline schedule
        if ($request->boolean('auto_generate_schedule', true)) {
            $this->builderService->generateBaselineSchedule($campaign);
        }

        return redirect()->route('admin.attendance.schedule', $campaign->id)
            ->with('success', "Campaign \"{$campaign->name}\" created with {$campaign->duration_days} days. You can now customize daily rewards.");
    }

    public function edit(AttendanceCampaign $campaign)
    {
        return view('admin.attendance.edit', compact('campaign'));
    }

    public function update(Request $request, AttendanceCampaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => "required|string|max:120|unique:attendance_campaigns,slug,{$campaign->id}",
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:3650',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'timezone' => 'required|string|max:64',
            'status' => 'required|in:draft,scheduled,active,paused,completed',
            'progression_mode' => 'required|in:cumulative,consecutive,scheduled',
            'reset_rule' => 'required|in:none,reset_to_start,freeze_streak',
            'participation_scope' => 'required|in:account,character',
            'delivery_destination' => 'required|in:inbox,storage,character',
            'display_order' => 'integer',
            'min_base_level' => 'integer|min:1',
            'min_account_age_days' => 'integer|min:0',
            'daily_reset_hour' => 'integer|min:0|max:23',
        ]);

        $validated['allow_character_choice'] = $request->has('allow_character_choice');
        $validated['require_in_game_login'] = $request->has('require_in_game_login');
        $validated['repeatable'] = $request->has('repeatable');

        $campaign->update($validated);

        return redirect()->route('admin.attendance.index')
            ->with('success', "Campaign \"{$campaign->name}\" updated successfully.");
    }

    public function destroy(AttendanceCampaign $campaign)
    {
        $name = $campaign->name;
        $campaign->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', "Campaign \"{$name}\" deleted.");
    }

    public function duplicate(AttendanceCampaign $campaign)
    {
        $newName = "Copy of " . $campaign->name;
        $newSlug = Str::slug($newName) . '-' . Str::random(4);

        $clone = $this->builderService->duplicateCampaign($campaign, $newName, $newSlug);

        return redirect()->route('admin.attendance.schedule', $clone->id)
            ->with('success', "Campaign duplicated successfully into draft \"{$clone->name}\".");
    }

    public function toggleStatus(AttendanceCampaign $campaign)
    {
        $campaign->status = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->save();

        return back()->with('success', "Campaign status changed to {$campaign->status}.");
    }

    /**
     * Reward Schedule Builder UI for a campaign.
     */
    public function schedule(Request $request, AttendanceCampaign $campaign)
    {
        $days = $campaign->days()->with('items')->get();

        // Check if any days are missing up to duration_days
        $existingDayNumbers = $days->pluck('day_number')->flip()->all();
        $missingDaysCount = 0;
        for ($d = 1; $d <= $campaign->duration_days; $d++) {
            if (!isset($existingDayNumbers[$d])) {
                $missingDaysCount++;
            }
        }

        return view('admin.attendance.schedule', compact('campaign', 'days', 'missingDaysCount'));
    }

    public function generateSchedule(Request $request, AttendanceCampaign $campaign)
    {
        $options = [
            'base_zeny' => (int) $request->input('base_zeny', 10000),
            'zeny_step' => (int) $request->input('zeny_step', 2500),
            'default_item_id' => (int) $request->input('default_item_id', 501),
            'default_item_qty' => (int) $request->input('default_item_qty', 5),
            'milestone_every' => (int) $request->input('milestone_every', 7),
        ];

        $count = $this->builderService->generateBaselineSchedule($campaign, $options);

        return back()->with('success', "Generated baseline reward schedule across {$campaign->duration_days} days ({$count} new day entries created).");
    }

    public function addItem(Request $request, AttendanceCampaign $campaign, int $dayNumber)
    {
        $validated = $request->validate([
            'reward_type' => 'required|in:zeny,item,cashpoints',
            'item_id' => 'nullable|integer',
            'item_name' => 'nullable|string|max:120',
            'quantity' => 'nullable|integer|min:1',
            'zeny_amount' => 'nullable|integer|min:0',
            'cashpoints_amount' => 'nullable|integer|min:0',
            'refine' => 'nullable|integer|min:0|max:20',
            'card0' => 'nullable|integer',
            'card1' => 'nullable|integer',
            'card2' => 'nullable|integer',
            'card3' => 'nullable|integer',
            'identified' => 'nullable|boolean',
            'bound' => 'nullable|integer',
            'delivery_method' => 'nullable|in:inbox,storage,character',
        ]);

        $day = AttendanceCampaignDay::firstOrCreate(
            ['campaign_id' => $campaign->id, 'day_number' => $dayNumber],
            ['title' => "Day {$dayNumber}", 'is_milestone' => ($dayNumber % 7 === 0)]
        );

        $item = $this->builderService->addItemToDay($day, $validated);

        return back()->with('success', "Added {$item->formatted_title} to Day {$dayNumber}.");
    }

    public function deleteItem(AttendanceCampaign $campaign, int $itemId)
    {
        $item = AttendanceCampaignItem::findOrFail($itemId);
        $item->delete();

        return back()->with('success', 'Reward item removed from schedule.');
    }

    public function updateDay(Request $request, AttendanceCampaign $campaign, int $dayNumber)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:120',
            'description' => 'nullable|string|max:255',
            'is_milestone' => 'boolean',
        ]);

        $day = AttendanceCampaignDay::firstOrCreate(
            ['campaign_id' => $campaign->id, 'day_number' => $dayNumber]
        );

        $day->update([
            'title' => $validated['title'] ?? $day->title,
            'description' => $validated['description'] ?? null,
            'is_milestone' => $request->has('is_milestone'),
        ]);

        return back()->with('success', "Day {$dayNumber} settings updated.");
    }

    /**
     * View all reward delivery logs with filter & manual retry.
     */
    public function deliveries(Request $request)
    {
        $query = AttendanceDelivery::with(['claim.campaign', 'claim.day', 'item']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reward_type')) {
            $query->where('reward_type', $request->reward_type);
        }

        $deliveries = $query->orderByDesc('id')->paginate(25);

        return view('admin.attendance.deliveries', compact('deliveries'));
    }

    public function retryDelivery(int $deliveryId)
    {
        $delivery = AttendanceDelivery::findOrFail($deliveryId);
        $success = $this->deliveryService->retryDelivery($delivery);

        if ($success) {
            return back()->with('success', "Delivery #{$delivery->id} retried and delivered successfully.");
        }

        return back()->with('error', "Delivery #{$delivery->id} retry failed: {$delivery->failure_reason}");
    }

    /**
     * Campaign Reports & Attendance Records.
     */
    public function reports(Request $request)
    {
        $query = AttendanceParticipation::with(['campaign', 'user', 'claims']);

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('account_id', $q)
                  ->orWhereHas('user', fn($u) => $u->where('username', 'like', "%{$q}%"));
            });
        }

        $participations = $query->orderByDesc('updated_at')->paginate(20);
        $campaigns = AttendanceCampaign::orderBy('name')->get();

        return view('admin.attendance.reports', compact('participations', 'campaigns'));
    }
}
