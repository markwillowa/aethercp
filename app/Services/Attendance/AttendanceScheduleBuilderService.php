<?php

namespace App\Services\Attendance;

use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Models\AttendanceCampaign;
use App\Models\AttendanceCampaignDay;
use App\Models\AttendanceCampaignItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceScheduleBuilderService
{
    public function __construct(
        protected ItemRepositoryInterface $itemRepo
    ) {}

    /**
     * Automatically generate a baseline reward schedule across any arbitrary duration
     * (e.g. 5, 7, 14, 30, 60, 90, 180, 365 days) with milestone scaling.
     */
    public function generateBaselineSchedule(AttendanceCampaign $campaign, array $options = []): int
    {
        $duration = (int) $campaign->duration_days;
        $baseZeny = (int) ($options['base_zeny'] ?? 10000);
        $zenyStep = (int) ($options['zeny_step'] ?? 2500);
        $defaultItemId = (int) ($options['default_item_id'] ?? 501); // Red Potion
        $defaultItemQty = (int) ($options['default_item_qty'] ?? 5);
        $milestoneEvery = (int) ($options['milestone_every'] ?? 7);

        // Milestone rewards pool for key intervals
        $milestonePool = [
            7 => ['nameid' => 603, 'name' => 'Old Blue Box', 'qty' => 1, 'zeny' => 50000],
            14 => ['nameid' => 616, 'name' => 'Old Card Album', 'qty' => 1, 'zeny' => 100000],
            21 => ['nameid' => 604, 'name' => 'Dead Branch Box', 'qty' => 1, 'zeny' => 150000],
            28 => ['nameid' => 617, 'name' => 'Old Purple Box', 'qty' => 2, 'zeny' => 250000],
            30 => ['nameid' => 616, 'name' => 'Old Card Album', 'qty' => 2, 'zeny' => 500000],
            60 => ['nameid' => 617, 'name' => 'Old Purple Box', 'qty' => 5, 'zeny' => 1000000],
            90 => ['nameid' => 12208, 'name' => 'Battle Manual Box', 'qty' => 1, 'zeny' => 2000000],
            180 => ['nameid' => 12214, 'name' => 'Bubble Gum Box', 'qty' => 2, 'zeny' => 5000000],
            365 => ['nameid' => 616, 'name' => 'Old Card Album', 'qty' => 10, 'zeny' => 10000000],
        ];

        return DB::transaction(function () use ($campaign, $duration, $baseZeny, $zenyStep, $defaultItemId, $defaultItemQty, $milestoneEvery, $milestonePool) {
            $createdCount = 0;

            for ($d = 1; $d <= $duration; $d++) {
                $isMilestone = ($d % $milestoneEvery === 0) || ($d === $duration) || isset($milestonePool[$d]);
                $title = $isMilestone ? "Milestone: Day {$d} Celebration" : "Day {$d} Reward";

                $day = AttendanceCampaignDay::updateOrCreate(
                    ['campaign_id' => $campaign->id, 'day_number' => $d],
                    [
                        'title' => $title,
                        'description' => "Attendance reward package for Day {$d}.",
                        'is_milestone' => $isMilestone,
                    ]
                );

                // If items already exist and overwrite is false, skip item regeneration
                if ($day->items()->count() > 0) {
                    continue;
                }

                $zenyAmount = $baseZeny + (($d - 1) * $zenyStep);

                // Add Zeny Reward
                AttendanceCampaignItem::create([
                    'campaign_day_id' => $day->id,
                    'reward_type' => 'zeny',
                    'zeny_amount' => $zenyAmount,
                ]);

                // Check for special milestone item
                if (isset($milestonePool[$d])) {
                    $mItem = $milestonePool[$d];
                    AttendanceCampaignItem::create([
                        'campaign_day_id' => $day->id,
                        'reward_type' => 'item',
                        'item_id' => $mItem['nameid'],
                        'item_name' => $mItem['name'],
                        'quantity' => $mItem['qty'],
                    ]);
                } elseif ($defaultItemId > 0) {
                    $itemDb = $this->itemRepo->findById($defaultItemId);
                    $itemName = $itemDb ? ($itemDb->name_english ?? "Item #{$defaultItemId}") : "Item #{$defaultItemId}";

                    AttendanceCampaignItem::create([
                        'campaign_day_id' => $day->id,
                        'reward_type' => 'item',
                        'item_id' => $defaultItemId,
                        'item_name' => $itemName,
                        'quantity' => $defaultItemQty,
                    ]);
                }

                $createdCount++;
            }

            return $createdCount;
        });
    }

    /**
     * Duplicate an existing campaign with all its days and reward items.
     */
    public function duplicateCampaign(AttendanceCampaign $source, string $newName, string $newSlug): AttendanceCampaign
    {
        return DB::transaction(function () use ($source, $newName, $newSlug) {
            $clone = $source->replicate([
                'status',
            ]);
            $clone->name = $newName;
            $clone->slug = $newSlug;
            $clone->status = 'draft';
            $clone->save();

            $sourceDays = $source->days()->with('items')->get();
            foreach ($sourceDays as $day) {
                $newDay = $day->replicate();
                $newDay->campaign_id = $clone->id;
                $newDay->save();

                foreach ($day->items as $item) {
                    $newItem = $item->replicate();
                    $newItem->campaign_day_id = $newDay->id;
                    $newItem->save();
                }
            }

            return $clone;
        });
    }

    /**
     * Add a reward item to an individual campaign day.
     */
    public function addItemToDay(AttendanceCampaignDay $day, array $data): AttendanceCampaignItem
    {
        $rewardType = $data['reward_type'] ?? 'item';
        $itemId = !empty($data['item_id']) ? (int) $data['item_id'] : null;
        $itemName = $data['item_name'] ?? null;

        if ($rewardType === 'item' && $itemId && empty($itemName)) {
            $itemDb = $this->itemRepo->findById($itemId);
            $itemName = $itemDb ? ($itemDb->name_english ?? "Item #{$itemId}") : "Item #{$itemId}";
        }

        return AttendanceCampaignItem::create([
            'campaign_day_id' => $day->id,
            'reward_type' => $rewardType,
            'item_id' => $itemId,
            'item_name' => $itemName,
            'quantity' => max(1, (int) ($data['quantity'] ?? 1)),
            'zeny_amount' => max(0, (int) ($data['zeny_amount'] ?? 0)),
            'cashpoints_amount' => max(0, (int) ($data['cashpoints_amount'] ?? 0)),
            'refine' => max(0, min(20, (int) ($data['refine'] ?? 0))),
            'card0' => (int) ($data['card0'] ?? 0),
            'card1' => (int) ($data['card1'] ?? 0),
            'card2' => (int) ($data['card2'] ?? 0),
            'card3' => (int) ($data['card3'] ?? 0),
            'identified' => (bool) ($data['identified'] ?? true),
            'bound' => (int) ($data['bound'] ?? 0),
            'delivery_method' => $data['delivery_method'] ?? null,
        ]);
    }
}
