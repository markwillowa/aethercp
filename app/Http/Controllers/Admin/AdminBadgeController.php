<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\EmulatorInterface;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BadgeAssignment;
use App\Models\BadgeDefinition;
use App\Models\BadgeTier;
use App\Services\Ranking\BadgeService;
use App\Services\Ranking\RankingEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminBadgeController extends Controller
{
    public function __construct(
        protected BadgeService $badgeService,
        protected RankingEngineService $rankingEngine,
        protected EmulatorInterface $emulator
    ) {}

    public function index()
    {
        $this->badgeService->ensureDefaultDefinitionsExist();

        $badges = BadgeDefinition::orderByDesc('priority')->orderBy('id')->get();
        $pvpTiers = BadgeTier::where('category', 'pvp')->orderBy('tier_order')->orderBy('stars')->get();
        $mvpTiers = BadgeTier::where('category', 'mvp')->orderBy('tier_order')->orderBy('stars')->get();
        $assignments = BadgeAssignment::with('definition')
            ->latest('awarded_at')
            ->limit(40)
            ->get();

        return view('admin.badges.index', compact('badges', 'pvpTiers', 'mvpTiers', 'assignments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'description' => 'nullable|string|max:255',
            'category' => 'required|in:level,job,pvp,mvp,guild,special,event,achievement',
            'icon_type' => 'required|in:lucide,image',
            'icon' => 'nullable|string|max:255',
            'icon_file' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'color' => 'required|string|max:32',
            'visual_style' => 'required|in:solid,outline,gradient,glow',
            'priority' => 'required|integer|min:0|max:1000',
            'auto_condition_type' => 'nullable|string|max:40',
            'auto_condition_value' => 'nullable|integer',
        ]);

        $iconValue = $validated['icon'] ?: 'award';
        $iconType = $validated['icon_type'];

        if ($iconType === 'image' && $request->hasFile('icon_file')) {
            $dir = public_path('uploads/badges');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $file = $request->file('icon_file');
            $filename = Str::slug($validated['name']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $iconValue = 'uploads/badges/' . $filename;
        }

        $slug = Str::slug($validated['name']);
        if (BadgeDefinition::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        BadgeDefinition::create([
            'slug' => $slug,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'icon_type' => $iconType,
            'icon' => $iconValue,
            'color' => $validated['color'],
            'visual_style' => $validated['visual_style'],
            'priority' => $validated['priority'],
            'is_enabled' => $request->has('is_enabled'),
            'is_permanent' => $request->has('is_permanent'),
            'auto_condition_type' => $validated['auto_condition_type'] ?: 'manual',
            'auto_condition_value' => $validated['auto_condition_value'] ?? null,
        ]);

        $this->rankingEngine->invalidateCache();
        ActivityLog::log('admin.badge_create', "Created badge [{$validated['name']}].");

        return back()->with('success', "Badge [{$validated['name']}] created successfully.");
    }

    public function update(Request $request, int $id)
    {
        $badge = BadgeDefinition::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'description' => 'nullable|string|max:255',
            'category' => 'required|in:level,job,pvp,mvp,guild,special,event,achievement',
            'icon_type' => 'required|in:lucide,image',
            'icon' => 'nullable|string|max:255',
            'icon_file' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'color' => 'required|string|max:32',
            'visual_style' => 'required|in:solid,outline,gradient,glow',
            'priority' => 'required|integer|min:0|max:1000',
            'auto_condition_type' => 'nullable|string|max:40',
        ]);

        $iconValue = $validated['icon'] ?: $badge->icon;
        $iconType = $validated['icon_type'];

        if ($iconType === 'image' && $request->hasFile('icon_file')) {
            $dir = public_path('uploads/badges');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $file = $request->file('icon_file');
            $filename = Str::slug($validated['name']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $iconValue = 'uploads/badges/' . $filename;
        }

        $badge->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'icon_type' => $iconType,
            'icon' => $iconValue,
            'color' => $validated['color'],
            'visual_style' => $validated['visual_style'],
            'priority' => $validated['priority'],
            'is_enabled' => $request->has('is_enabled'),
            'is_permanent' => $request->has('is_permanent'),
            'auto_condition_type' => $validated['auto_condition_type'] ?: 'manual',
        ]);

        BadgeAssignment::where('badge_id', $badge->id)
            ->where('is_active', true)
            ->update([
                'label' => $badge->name,
                'color' => $badge->color,
            ]);

        $this->rankingEngine->invalidateCache();
        ActivityLog::log('admin.badge_update', "Updated badge [{$badge->name}].");

        return back()->with('success', "Badge [{$badge->name}] updated.");
    }

    public function toggle(int $id)
    {
        $badge = BadgeDefinition::findOrFail($id);
        $badge->is_enabled = !$badge->is_enabled;
        $badge->save();

        $this->rankingEngine->invalidateCache();
        ActivityLog::log('admin.badge_toggle', "Toggled badge [{$badge->name}] to " . ($badge->is_enabled ? 'enabled' : 'disabled') . '.');

        return back()->with('success', "Badge [{$badge->name}] " . ($badge->is_enabled ? 'enabled' : 'disabled') . '.');
    }

    public function updateTiers(Request $request)
    {
        $tiersInput = $request->input('tiers', []);
        foreach ($tiersInput as $tierId => $data) {
            $tier = BadgeTier::find($tierId);
            if ($tier) {
                $tier->min_kills = max(0, (int) ($data['min_kills'] ?? $tier->min_kills));
                $tier->color = (string) ($data['color'] ?? $tier->color);
                $tier->is_enabled = !empty($data['is_enabled']);
                $tier->save();
            }
        }

        $this->badgeService->recalculateAll();
        $this->rankingEngine->invalidateCache();
        ActivityLog::log('admin.badge_tiers_update', 'Updated PvP/MVP badge tier thresholds and recalculated badges.');

        return back()->with('success', 'Tier thresholds saved and all player badges recalculated.');
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'badge_id' => 'required|exists:badge_definitions,id',
            'target_type' => 'required|in:character,guild',
            'target_identifier' => 'required|string|max:60',
            'notes' => 'nullable|string|max:255',
        ]);

        $badge = BadgeDefinition::findOrFail($validated['badge_id']);
        $identifier = trim($validated['target_identifier']);
        $conn = DB::connection('ragnarok');
        $targetId = 0;

        if ($validated['target_type'] === 'character') {
            $charTable = $this->emulator->getTableName('char');
            $char = is_numeric($identifier)
                ? $conn->table($charTable)->where('char_id', (int) $identifier)->first()
                : $conn->table($charTable)->where('name', $identifier)->first();
            if (!$char) {
                return back()->with('error', "Character [{$identifier}] not found in rAthena.");
            }
            $targetId = (int) $char->char_id;
        } else {
            $guildTable = $this->emulator->getTableName('guild');
            $guild = is_numeric($identifier)
                ? $conn->table($guildTable)->where('guild_id', (int) $identifier)->first()
                : $conn->table($guildTable)->where('name', $identifier)->first();
            if (!$guild) {
                return back()->with('error', "Guild [{$identifier}] not found in rAthena.");
            }
            $targetId = (int) $guild->guild_id;
        }

        $assignment = $this->badgeService->assignDefinitionBadge(
            $badge,
            $validated['target_type'],
            $targetId,
            'admin',
            auth()->id(),
            $validated['notes'] ?? null
        );

        if (!$assignment) {
            return back()->with('error', 'This target already has this active badge.');
        }

        $this->rankingEngine->invalidateCache();
        ActivityLog::log('admin.badge_assign', "Manually assigned badge [{$badge->name}] to {$validated['target_type']} #{$targetId}.");

        return back()->with('success', "Badge [{$badge->name}] awarded to {$validated['target_type']} #{$targetId}.");
    }

    public function revoke(int $id)
    {
        $assignment = BadgeAssignment::findOrFail($id);
        $assignment->is_active = false;
        $assignment->revoked_at = now();
        $assignment->save();

        $this->rankingEngine->invalidateCache();
        ActivityLog::log('admin.badge_revoke', "Revoked badge assignment #{$assignment->id} ({$assignment->label}).");

        return back()->with('success', "Badge [{$assignment->label}] revoked.");
    }

    public function recalculate()
    {
        $res = $this->badgeService->recalculateAll();
        $this->rankingEngine->invalidateCache();
        ActivityLog::log('admin.badge_recalculate', "Recalculated badges across {$res['characters_checked']} characters ({$res['new_badges_awarded']} newly awarded).");

        return back()->with('success', "Recalculated {$res['characters_checked']} characters and all guild ranks ({$res['new_badges_awarded']} new badges awarded).");
    }
}
