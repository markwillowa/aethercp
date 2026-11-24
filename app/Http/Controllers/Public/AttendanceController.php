<?php

namespace App\Http\Controllers\Public;

use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\AttendanceCampaign;
use App\Models\PlayerInboxItem;
use App\Services\Attendance\AttendanceDeliveryService;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected AttendanceDeliveryService $deliveryService,
        protected CharacterRepositoryInterface $characterRepo
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $characters = $user ? $this->characterRepo->getCharactersByAccount((int) $user->account_id) : [];

        $campaignsData = $user
            ? $this->attendanceService->getActiveCampaignsForUser($user)
            : [];

        // If guest or no participation yet, load active campaigns for display
        if (!$user) {
            $guestCampaigns = AttendanceCampaign::where('status', 'active')->orderBy('display_order')->get();
            foreach ($guestCampaigns as $c) {
                if ($c->isActive()) {
                    $campaignsData[] = [
                        'campaign' => $c,
                        'participation' => null,
                        'eligibility' => ['eligible' => false, 'reason' => 'Log in to participate in daily rewards.'],
                        'days' => $c->days()->with('items')->get()->map(fn($d) => [
                            'day_number' => $d->day_number,
                            'title' => $d->title,
                            'description' => $d->description,
                            'is_milestone' => $d->is_milestone,
                            'status' => 'locked',
                            'items' => $d->items,
                        ])->all(),
                        'next_claimable_day' => null,
                        'can_claim_today' => false,
                    ];
                }
            }
        }

        $inboxCount = $user
            ? PlayerInboxItem::where('account_id', $user->account_id)->where('status', 'unclaimed')->count()
            : 0;

        $activeSlug = $request->query('campaign', $campaignsData[0]['campaign']->slug ?? null);

        return view('attendance.index', compact('campaignsData', 'characters', 'inboxCount', 'activeSlug'));
    }

    public function claim(Request $request, AttendanceCampaign $campaign)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please sign in to claim daily rewards.'], 401);
        }

        $validated = $request->validate([
            'day_number' => 'required|integer|min:1',
            'character_id' => 'nullable|integer',
        ]);

        $result = $this->attendanceService->claimReward(
            $campaign,
            $user,
            (int) $validated['day_number'],
            !empty($validated['character_id']) ? (int) $validated['character_id'] : null
        );

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function inbox(Request $request)
    {
        $user = Auth::user();
        $items = PlayerInboxItem::where('account_id', $user->account_id)
            ->orderByDesc('id')
            ->paginate(15);

        return view('attendance.inbox', compact('items'));
    }

    public function claimToStorage(Request $request, PlayerInboxItem $item)
    {
        $user = Auth::user();
        $result = $this->deliveryService->claimInboxItemToStorage($item, $user);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
