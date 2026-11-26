<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Character\CharacterManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PlayerCharacterServiceController extends Controller
{
    public function __construct(private readonly CharacterManagementService $service) {}

    public function manage(int $charId)
    {
        try {
            $detail = $this->service->getCharacterDetailForPlayer(Auth::user(), $charId);
            $detail['idempotencyKey'] = (string) Str::uuid();
            return view('account.manage-character', $detail);
        } catch (\Throwable $e) {
            return redirect()->route('account.characters')->with('error', $e->getMessage());
        }
    }

    public function rename(Request $request, int $charId)
    {
        $validated = $request->validate([
            'new_name' => 'required|string|min:4|max:23',
            'idempotency_key' => 'nullable|string|max:120',
        ]);

        try {
            $this->service->renameCharacter(
                Auth::user(),
                $charId,
                $validated['new_name'],
                $validated['idempotency_key'] ?: (string) Str::uuid(),
                $request->ip()
            );
            return back()->with('success', 'Character renamed successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function appearance(Request $request, int $charId)
    {
        $validated = $request->validate([
            'hair' => 'nullable|integer|min:0|max:200',
            'hair_color' => 'nullable|integer|min:0|max:200',
            'clothes_color' => 'nullable|integer|min:0|max:200',
            'idempotency_key' => 'nullable|string|max:120',
        ]);

        try {
            $this->service->changeAppearance(
                Auth::user(),
                $charId,
                $validated,
                $validated['idempotency_key'] ?: (string) Str::uuid(),
                $request->ip()
            );
            return back()->with('success', 'Character appearance updated successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resetStats(Request $request, int $charId)
    {
        try {
            $this->service->resetStats(
                Auth::user(),
                $charId,
                $request->input('idempotency_key') ?: (string) Str::uuid(),
                $request->ip()
            );
            return back()->with('success', 'Character stats have been reset and status points refunded.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resetSkills(Request $request, int $charId)
    {
        try {
            $this->service->resetSkills(
                Auth::user(),
                $charId,
                $request->input('idempotency_key') ?: (string) Str::uuid(),
                $request->ip()
            );
            return back()->with('success', 'Character skills have been reset and skill points refunded.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changeGender(Request $request, int $charId)
    {
        $validated = $request->validate([
            'gender' => 'required|in:M,F',
            'idempotency_key' => 'nullable|string|max:120',
        ]);

        try {
            $this->service->changeAccountGender(
                Auth::user(),
                $charId,
                $validated['gender'],
                $validated['idempotency_key'] ?: (string) Str::uuid(),
                $request->ip()
            );
            return back()->with('success', 'Game account gender updated successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changeJob(Request $request, int $charId)
    {
        $validated = $request->validate([
            'target_class' => 'required|integer|min:0',
            'idempotency_key' => 'nullable|string|max:120',
        ]);

        try {
            $this->service->changeJob(
                Auth::user(),
                $charId,
                (int) $validated['target_class'],
                $validated['idempotency_key'] ?: (string) Str::uuid(),
                $request->ip()
            );
            return back()->with('success', 'Character job class updated successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleLock(Request $request, int $charId)
    {
        $validated = $request->validate([
            'action' => 'required|in:lock,unlock',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $this->service->togglePlayerLock(
                Auth::user(),
                $charId,
                $validated['action'] === 'lock',
                $validated['reason'] ?? 'Player self-lock',
                $request->ip()
            );
            return back()->with('success', $validated['action'] === 'lock' ? 'Character locked.' : 'Character unlocked.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function requestDeletion(Request $request, int $charId)
    {
        try {
            $this->service->requestDeletion(Auth::user(), $charId, $request->ip());
            return back()->with('success', 'Deletion scheduled. You may cancel it before the waiting period expires.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancelDeletion(Request $request, int $charId)
    {
        try {
            $this->service->cancelDeletion(Auth::user(), $charId, $request->ip());
            return back()->with('success', 'Character deletion request cancelled.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function requestRecovery(Request $request, int $charId)
    {
        try {
            $this->service->requestRecovery(Auth::user(), $charId, $request->ip());
            return back()->with('success', 'Recovery request submitted for administrator review.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
