<?php

namespace App\Http\Controllers\Public;

use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceTransaction;
use App\Services\Marketplace\MarketplaceEscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    public function __construct(
        protected MarketplaceEscrowService $escrowService,
        protected CharacterRepositoryInterface $characterRepo
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $settings = $this->escrowService->getSettings();

        // Automatically clean up expired listings
        $this->escrowService->expireOutdatedListings();

        // 1. Fetch user's characters
        $characters = $this->characterRepo->getCharactersByAccount($user->account_id);

        if (empty($characters)) {
            return view('marketplace.no_character', compact('settings'));
        }

        // 2. Active character in session
        $activeCharId = (int) session('active_marketplace_char_id', 0);
        $activeChar = null;

        if ($activeCharId > 0) {
            foreach ($characters as $char) {
                if ((int) $char->char_id === $activeCharId) {
                    $activeChar = $char;
                    break;
                }
            }
        }

        // Default to first character if not set or invalid
        if (!$activeChar) {
            $activeChar = $characters[0];
            $activeCharId = (int) $activeChar->char_id;
            session(['active_marketplace_char_id' => $activeCharId]);
        }

        // Live balances
        $characterZeny = $this->escrowService->getCharacterZeny($activeCharId);
        $accountCashPoints = $this->escrowService->getAccountCashPoints($user->account_id);

        // 3. Determine current tab
        $tab = $request->query('tab', 'browse');
        $validTabs = ['browse', 'storage', 'custody', 'my_listings', 'history'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'browse';
        }

        // Tab Data
        $browseListings = null;
        $storageItems = [];
        $custodyItems = [];
        $myListings = [];
        $history = [];

        if ($tab === 'browse') {
            $query = MarketplaceListing::with(['item', 'sellerUser'])
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->where('remaining_quantity', '>', 0);

            // Filter keyword (name or item ID)
            if ($keyword = trim($request->query('q', ''))) {
                $query->whereHas('item', function ($q) use ($keyword) {
                    if (is_numeric($keyword)) {
                        $q->where('nameid', (int) $keyword)
                          ->orWhere('item_name', 'like', "%{$keyword}%");
                    } else {
                        $q->where('item_name', 'like', "%{$keyword}%");
                    }
                });
            }

            // Filter type / category
            if ($type = $request->query('type')) {
                if ($type !== 'all') {
                    $query->whereHas('item', function ($q) use ($type) {
                        $q->where('item_type', $type);
                    });
                }
            }

            // Filter currency
            if ($currency = $request->query('currency')) {
                if (in_array($currency, ['zeny', 'cashpoints'])) {
                    $query->where('currency_type', $currency);
                }
            }

            // Sort
            $sort = $request->query('sort', 'newest');
            match ($sort) {
                'price_asc' => $query->orderBy('unit_price', 'asc'),
                'price_desc' => $query->orderBy('unit_price', 'desc'),
                default => $query->latest(),
            };

            $browseListings = $query->paginate(15)->withQueryString();
        } elseif ($tab === 'storage') {
            $storageItems = $this->escrowService->getStorageItems($user->account_id);
        } elseif ($tab === 'custody') {
            $custodyItems = MarketplaceItem::where('user_id', $user->id)
                ->where('status', 'in_custody')
                ->latest()
                ->get();
        } elseif ($tab === 'my_listings') {
            $statusFilter = $request->query('status', 'all');
            $myListingsQuery = MarketplaceListing::with('item')
                ->where('seller_user_id', $user->id);

            if ($statusFilter !== 'all' && in_array($statusFilter, ['active', 'sold', 'cancelled', 'expired'])) {
                $myListingsQuery->where('status', $statusFilter);
            }

            $myListings = $myListingsQuery->latest()->paginate(15)->withQueryString();
        } elseif ($tab === 'history') {
            $history = MarketplaceTransaction::with(['item', 'sellerUser', 'buyerUser'])
                ->where(function ($q) use ($user) {
                    $q->where('seller_user_id', $user->id)
                      ->orWhere('buyer_user_id', $user->id);
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        $listings = $browseListings;

        return view('marketplace.index', compact(
            'settings',
            'characters',
            'activeChar',
            'characterZeny',
            'accountCashPoints',
            'tab',
            'browseListings',
            'listings',
            'storageItems',
            'custodyItems',
            'myListings',
            'history'
        ));
    }

    public function selectCharacter(Request $request)
    {
        $request->validate([
            'char_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $charId = (int) $request->input('char_id');

        $char = $this->escrowService->getAccountCharacter($charId, $user->account_id);
        if (!$char) {
            return back()->with('error', 'Selected character does not belong to your account.');
        }

        session(['active_marketplace_char_id' => $charId]);

        return back()->with('success', "Active marketplace character switched to {$char->name}.");
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'storage_id' => 'required|integer',
            'amount' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $activeCharId = (int) session('active_marketplace_char_id');

        try {
            $item = $this->escrowService->transferToCustody(
                $user,
                $activeCharId,
                (int) $request->input('storage_id'),
                (int) $request->input('amount')
            );

            return redirect()->route('marketplace.index', ['tab' => 'custody'])
                ->with('success', "Transferred {$item->amount}x {$item->formatted_name} to Trade House custody.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createListing(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'currency_type' => 'required|in:zeny,cashpoints',
            'unit_price' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'duration_hours' => 'required|integer|in:24,72,168,336,720',
        ]);

        $user = Auth::user();
        $activeCharId = (int) session('active_marketplace_char_id');

        try {
            $listing = $this->escrowService->createListing(
                $user,
                $activeCharId,
                (int) $request->input('item_id'),
                $request->input('currency_type'),
                (int) $request->input('unit_price'),
                (int) $request->input('quantity'),
                (int) $request->input('duration_hours')
            );

            return redirect()->route('marketplace.index', ['tab' => 'my_listings'])
                ->with('success', "Listing [{$listing->listing_code}] created successfully!");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(int $id)
    {
        $user = Auth::user();

        try {
            $this->escrowService->cancelListing($user, $id);
            return back()->with('success', 'Listing cancelled. The item has been returned to your custody inventory.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function withdraw(int $id)
    {
        $user = Auth::user();
        $activeCharId = (int) session('active_marketplace_char_id');

        try {
            $this->escrowService->withdrawToStorage($user, $activeCharId, $id);
            return redirect()->route('marketplace.index', ['tab' => 'storage'])
                ->with('success', 'Item successfully withdrawn back to your Kafra storage.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function buy(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $activeCharId = (int) session('active_marketplace_char_id');

        try {
            $tx = $this->escrowService->purchaseListing(
                $buyer = $user,
                $charId = $activeCharId,
                $listingId = $id,
                $quantity = (int) $request->input('quantity', 1)
            );

            return redirect()->route('marketplace.index', ['tab' => 'history'])
                ->with('success', "Purchase [{$tx->transaction_code}] complete! {$tx->quantity}x {$tx->item->formatted_name} delivered to your Kafra storage.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
