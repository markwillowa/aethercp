<?php

namespace App\Http\Controllers\Public;

use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\PlayerInboxItem;
use App\Services\Escrow\EscrowInboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EscrowInboxController extends Controller
{
    public function __construct(
        protected EscrowInboxService $escrowService,
        protected CharacterRepositoryInterface $characterRepo
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $characters = $this->characterRepo->getCharactersByAccount((int) $user->account_id);

        $source = (string) $request->query('source', 'all');
        $status = (string) $request->query('status', 'unclaimed');

        $query = PlayerInboxItem::where('account_id', $user->account_id);

        if ($source !== 'all') {
            if ($source === 'attendance' || $source === 'daily') {
                $query->whereIn('source_type', ['attendance', 'daily']);
            } else {
                $query->where('source_type', $source);
            }
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $items = $query->orderByDesc('id')->paginate(12)->withQueryString();

        $counts = [
            'total' => PlayerInboxItem::where('account_id', $user->account_id)->count(),
            'unclaimed' => PlayerInboxItem::where('account_id', $user->account_id)->where('status', 'unclaimed')->count(),
            'claimed' => PlayerInboxItem::where('account_id', $user->account_id)->where('status', 'claimed')->count(),
            'attendance' => PlayerInboxItem::where('account_id', $user->account_id)->whereIn('source_type', ['attendance', 'daily'])->count(),
            'marketplace' => PlayerInboxItem::where('account_id', $user->account_id)->where('source_type', 'marketplace')->count(),
            'ranking' => PlayerInboxItem::where('account_id', $user->account_id)->where('source_type', 'ranking')->count(),
            'donation' => PlayerInboxItem::where('account_id', $user->account_id)->where('source_type', 'donation')->count(),
        ];

        return view('escrow.index', compact('items', 'characters', 'counts', 'source', 'status'));
    }

    public function claimToStorage(Request $request, PlayerInboxItem $item)
    {
        $user = Auth::user();
        $result = $this->escrowService->claimToStorage($item, $user);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function claimToCharacter(Request $request, PlayerInboxItem $item)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'character_id' => 'required|integer',
        ]);

        $result = $this->escrowService->claimToCharacter($item, $user, (int) $validated['character_id']);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
