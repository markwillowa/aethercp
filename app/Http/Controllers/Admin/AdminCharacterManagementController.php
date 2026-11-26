<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Character\CharacterManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCharacterManagementController extends Controller
{
    public function __construct(private readonly CharacterManagementService $service) {}

    private function authorizePermission(string $permission): void
    {
        if (!Auth::user()?->hasCharMgmtPermission($permission)) {
            abort(403, "Missing character management permission: {$permission}");
        }
    }

    public function index(Request $request)
    {
        $this->authorizePermission('view_characters');

        $characters = $this->service->getAdminCharacters($request->only([
            'q', 'online', 'class', 'min_level', 'max_level', 'locked',
        ]));

        return view('admin.characters.index', [
            'characters' => $characters,
            'jobs' => config('jobs', []),
            'filters' => $request->only(['q', 'online', 'class', 'min_level', 'max_level', 'locked']),
        ]);
    }

    public function show(int $charId)
    {
        $this->authorizePermission('view_characters');

        $detail = $this->service->getCharacterDetailForAdmin($charId);
        $detail['jobs'] = config('jobs', []);

        return view('admin.characters.show', $detail);
    }

    public function update(Request $request, int $charId)
    {
        $this->authorizePermission('edit_characters');

        $validated = $request->validate([
            'name' => 'nullable|string|min:4|max:23',
            'class' => 'nullable|integer|min:0',
            'base_level' => 'nullable|integer|min:1|max:999',
            'job_level' => 'nullable|integer|min:1|max:999',
            'base_exp' => 'nullable|integer|min:0',
            'job_exp' => 'nullable|integer|min:0',
            'zeny' => 'nullable|integer|min:0|max:2147483647',
            'str' => 'nullable|integer|min:1|max:999',
            'agi' => 'nullable|integer|min:1|max:999',
            'vit' => 'nullable|integer|min:1|max:999',
            'int' => 'nullable|integer|min:1|max:999',
            'dex' => 'nullable|integer|min:1|max:999',
            'luk' => 'nullable|integer|min:1|max:999',
            'pow' => 'nullable|integer|min:0|max:999',
            'sta' => 'nullable|integer|min:0|max:999',
            'wis' => 'nullable|integer|min:0|max:999',
            'spl' => 'nullable|integer|min:0|max:999',
            'con' => 'nullable|integer|min:0|max:999',
            'crt' => 'nullable|integer|min:0|max:999',
            'status_point' => 'nullable|integer|min:0',
            'skill_point' => 'nullable|integer|min:0',
            'trait_point' => 'nullable|integer|min:0',
            'hair' => 'nullable|integer|min:0|max:100',
            'hair_color' => 'nullable|integer|min:0|max:100',
            'clothes_color' => 'nullable|integer|min:0|max:100',
            'last_map' => 'nullable|string|max:32',
            'last_x' => 'nullable|integer|min:0|max:1024',
            'last_y' => 'nullable|integer|min:0|max:1024',
            'save_map' => 'nullable|string|max:32',
            'save_x' => 'nullable|integer|min:0|max:1024',
            'save_y' => 'nullable|integer|min:0|max:1024',
        ]);

        try {
            $this->service->adminUpdateCharacter(Auth::user(), $charId, $validated, $request->ip());
            return back()->with('success', 'Character attributes updated and audited.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function moderate(Request $request, int $charId)
    {
        $this->authorizePermission('moderate_characters');

        $validated = $request->validate([
            'action' => 'required|in:force_logout,lock,unlock,move_save_point,reset_stats,reset_skills,approve_recovery',
            'reason' => 'nullable|string|max:255',
            'lock_scope' => 'nullable|in:services,full',
        ]);

        try {
            $message = $this->service->adminModerate(Auth::user(), $charId, $validated['action'], $validated, $request->ip());
            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function giveItem(Request $request, int $charId)
    {
        $this->authorizePermission('manage_items');

        $validated = $request->validate([
            'item_id' => 'required|integer|min:1',
            'amount' => 'required|integer|min:1|max:30000',
            'refine' => 'nullable|integer|min:0|max:20',
            'delivery' => 'required|in:escrow,direct',
        ]);

        try {
            $this->service->adminGiveItem(
                Auth::user(),
                $charId,
                (int) $validated['item_id'],
                (int) $validated['amount'],
                (int) ($validated['refine'] ?? 0),
                $request->boolean('is_bound'),
                $validated['delivery'],
                $request->ip()
            );
            return back()->with('success', 'Item delivered successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function modifyContainerItem(Request $request, int $charId, string $container, int $rowId)
    {
        $this->authorizePermission('manage_items');

        $validated = $request->validate([
            'action' => 'required|in:remove,unequip,update',
            'amount' => 'nullable|integer|min:1|max:30000',
            'refine' => 'nullable|integer|min:0|max:20',
        ]);

        try {
            $this->service->adminModifyContainerItem(Auth::user(), $charId, $container, $rowId, $validated['action'], $validated, $request->ip());
            return back()->with('success', 'Container item updated.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function refundTransaction(Request $request, int $charId, int $transactionId)
    {
        $this->authorizePermission('refund_services');

        $validated = $request->validate([
            'reason' => 'required|string|min:3|max:255',
        ]);

        try {
            $this->service->adminRefundTransaction(Auth::user(), $transactionId, $validated['reason'], $request->ip());
            return back()->with('success', 'Cash Points refunded and transaction marked as refunded.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addNote(Request $request, int $charId)
    {
        $this->authorizePermission('view_characters');

        $validated = $request->validate([
            'note' => 'required|string|min:2|max:1000',
        ]);

        $this->service->addAdminNote(Auth::user(), $charId, $validated['note']);
        return back()->with('success', 'Internal admin note added.');
    }

    public function settings()
    {
        $this->authorizePermission('configure_services');

        return view('admin.characters.settings', [
            'settings' => $this->service->getSettings(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $this->authorizePermission('configure_services');

        $validated = $request->validate([
            'rename_cost' => 'required|integer|min:0',
            'rename_cooldown_hours' => 'required|integer|min:0',
            'rename_min_level' => 'required|integer|min:1',
            'rename_max_level' => 'required|integer|min:1',
            'rename_blocked_words' => 'nullable|string|max:500',
            'appearance_cost' => 'required|integer|min:0',
            'appearance_cooldown_hours' => 'required|integer|min:0',
            'appearance_max_hair' => 'required|integer|min:1',
            'appearance_max_hair_color' => 'required|integer|min:1',
            'appearance_max_clothes_color' => 'required|integer|min:1',
            'stat_reset_cost' => 'required|integer|min:0',
            'stat_reset_cooldown_hours' => 'required|integer|min:0',
            'stat_reset_min_level' => 'required|integer|min:1',
            'skill_reset_cost' => 'required|integer|min:0',
            'skill_reset_cooldown_hours' => 'required|integer|min:0',
            'skill_reset_min_level' => 'required|integer|min:1',
            'gender_cost' => 'required|integer|min:0',
            'gender_cooldown_hours' => 'required|integer|min:0',
            'deletion_wait_hours' => 'required|integer|min:1|max:720',
        ]);

        $booleans = [
            'rename_enabled', 'appearance_enabled', 'stat_reset_enabled',
            'skill_reset_enabled', 'gender_enabled', 'deletion_enabled',
            'deletion_allow_recovery', 'lock_enabled',
        ];
        foreach ($booleans as $key) {
            $validated[$key] = $request->boolean($key);
        }
        $validated['rename_blocked_words'] = $validated['rename_blocked_words'] ?? '';

        $this->service->updateSettings($validated, Auth::user());

        return back()->with('success', 'Character Management settings saved.');
    }
}
