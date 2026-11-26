<?php

namespace App\Http\Controllers\Public;

use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\RedeemRedemption;
use App\Services\Redeem\RedeemRedemptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedeemCodeController extends Controller
{
    public function __construct(
        protected RedeemRedemptionService $redemptionService,
        protected CharacterRepositoryInterface $characterRepo
    ) {}

    public function index()
    {
        $user = Auth::user();
        $characters = [];
        if (!empty($user->account_id)) {
            try {
                $characters = $this->characterRepo->getCharactersByAccount((int) $user->account_id);
            } catch (\Throwable $e) {
                $characters = [];
            }
        }

        $history = RedeemRedemption::with(['campaign', 'deliveries'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(10);

        return view('account.redeem', compact('user', 'characters', 'history'));
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64',
            'character_id' => 'nullable|integer',
        ]);

        $user = Auth::user();
        $preview = $this->redemptionService->previewCode(
            $user,
            $validated['code'],
            !empty($validated['character_id']) ? (int) $validated['character_id'] : null
        );

        if (!$preview['ok']) {
            return back()->withInput()->with('error', $preview['message']);
        }

        return back()->withInput()->with('redeem_preview', [
            'code' => strtoupper(trim($validated['code'])),
            'campaign_name' => $preview['campaign']->name,
            'campaign_description' => $preview['campaign']->description,
            'instructions' => $preview['campaign']->instructions,
            'requires_character' => $preview['requires_character'],
            'character_id' => !empty($validated['character_id']) ? (int) $validated['character_id'] : null,
            'rewards' => $preview['rewards']->map(fn ($r) => [
                'type' => $r->reward_type,
                'label' => $r->formattedLabel(),
                'scope' => $r->delivery_scope,
            ])->values()->all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64',
            'character_id' => 'nullable|integer',
        ]);

        $result = $this->redemptionService->redeem(
            Auth::user(),
            $validated['code'],
            !empty($validated['character_id']) ? (int) $validated['character_id'] : null,
            $request->ip()
        );

        if ($result['ok']) {
            return redirect()->route('account.redeem.index')->with('success', $result['message']);
        }

        if (!empty($result['partial'])) {
            return redirect()->route('account.redeem.index')->with('error', $result['message']);
        }

        return back()->withInput()->with('error', $result['message']);
    }
}
