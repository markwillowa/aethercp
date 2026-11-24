<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceTransaction;
use App\Models\SiteSetting;
use App\Models\ActivityLog;
use App\Services\Marketplace\MarketplaceEscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMarketplaceController extends Controller
{
    public function __construct(
        protected MarketplaceEscrowService $escrowService
    ) {}

    public function index(Request $request)
    {
        $settings = $this->escrowService->getSettings();
        $activeTab = $request->query('tab', 'listings');

        // 1. Overview Statistics
        $stats = [
            'active_listings' => MarketplaceListing::where('status', 'active')->where('expires_at', '>', now())->count(),
            'total_items_in_custody' => MarketplaceItem::where('status', 'in_custody')->count(),
            'total_items_all' => MarketplaceItem::count(),
            'total_sales_count' => MarketplaceTransaction::where('status', 'completed')->count(),
            'zeny_volume' => (int) MarketplaceTransaction::where('status', 'completed')->where('currency_type', 'zeny')->sum('gross_amount'),
            'zeny_tax_collected' => (int) MarketplaceTransaction::where('status', 'completed')->where('currency_type', 'zeny')->sum('tax_amount'),
            'points_volume' => (int) MarketplaceTransaction::where('status', 'completed')->where('currency_type', 'cashpoints')->sum('gross_amount'),
            'points_tax_collected' => (int) MarketplaceTransaction::where('status', 'completed')->where('currency_type', 'cashpoints')->sum('tax_amount'),
        ];

        // 2. Listings with comprehensive filters & search
        $listingsQuery = MarketplaceListing::with(['item', 'sellerUser'])->latest();

        if ($search = trim((string) $request->query('search', ''))) {
            $listingsQuery->where(function ($q) use ($search) {
                $q->where('listing_code', 'like', "%{$search}%")
                  ->orWhere('seller_char_name', 'like', "%{$search}%")
                  ->orWhereHas('item', function ($iq) use ($search) {
                      $iq->where('nameid', 'like', "%{$search}%");
                  });
            });
        }

        if ($filterStatus = $request->query('status')) {
            if ($filterStatus !== 'all') {
                $listingsQuery->where('status', $filterStatus);
            }
        }

        if ($filterCurrency = $request->query('currency')) {
            if (in_array($filterCurrency, ['zeny', 'cashpoints'])) {
                $listingsQuery->where('currency_type', $filterCurrency);
            }
        }

        $listings = $listingsQuery->paginate(20, ['*'], 'listings_page')->withQueryString();

        // 3. Escrow Custody Vault (All Items deposited by players)
        $custodyQuery = MarketplaceItem::with(['user', 'activeListing'])->latest();

        if ($custodySearch = trim((string) $request->query('custody_search', ''))) {
            $custodyQuery->where(function ($q) use ($custodySearch) {
                $q->where('nameid', 'like', "%{$custodySearch}%")
                  ->orWhere('account_id', 'like', "%{$custodySearch}%")
                  ->orWhereHas('user', function ($uq) use ($custodySearch) {
                      $uq->where('username', 'like', "%{$custodySearch}%");
                  });
            });
        }

        $custodyStatus = $request->query('custody_status', 'all');
        if ($custodyStatus && $custodyStatus !== 'all') {
            $custodyQuery->where('status', $custodyStatus);
        }

        $custodyItems = $custodyQuery->paginate(20, ['*'], 'custody_page')->withQueryString();

        // 4. Transactions Audit Log & Ledger
        $txQuery = MarketplaceTransaction::with(['item', 'sellerUser', 'buyerUser'])->latest();

        if ($txSearch = trim((string) $request->query('tx_search', ''))) {
            $txQuery->where(function ($q) use ($txSearch) {
                $q->where('transaction_code', 'like', "%{$txSearch}%")
                  ->orWhere('buyer_char_name', 'like', "%{$txSearch}%")
                  ->orWhere('seller_char_name', 'like', "%{$txSearch}%");
            });
        }

        if ($txCurrency = $request->query('tx_currency')) {
            if (in_array($txCurrency, ['zeny', 'cashpoints'])) {
                $txQuery->where('currency_type', $txCurrency);
            }
        }

        $transactions = $txQuery->paginate(20, ['*'], 'tx_page')->withQueryString();

        return view('admin.marketplace.index', compact('settings', 'stats', 'listings', 'custodyItems', 'transactions', 'activeTab'));
    }

    public function updateSettings(Request $request)
    {
        $fields = [
            'module_enabled_marketplace' => $request->has('module_enabled_marketplace') ? '1' : '0',
            'marketplace_maintenance_mode' => $request->has('marketplace_maintenance_mode') ? '1' : '0',
            'marketplace_announcement' => $request->input('marketplace_announcement', ''),
            'marketplace_rules' => $request->input('marketplace_rules', ''),
            'marketplace_max_listings_player' => max(1, (int) $request->input('marketplace_max_listings_player', 15)),
            'marketplace_max_listings_char' => max(1, (int) $request->input('marketplace_max_listings_char', 8)),
            'marketplace_max_items_per_listing' => max(1, (int) $request->input('marketplace_max_items_per_listing', 1000)),
            'marketplace_min_price' => max(1, (int) $request->input('marketplace_min_price', 1)),
            'marketplace_max_price' => max(1, (int) $request->input('marketplace_max_price', 1000000000)),
            'marketplace_default_duration_hours' => (int) $request->input('marketplace_default_duration_hours', 72),
            'marketplace_min_duration_hours' => max(1, (int) $request->input('marketplace_min_duration_hours', 24)),
            'marketplace_max_duration_hours' => max(24, (int) $request->input('marketplace_max_duration_hours', 720)),
            'marketplace_tax_percent' => max(0.0, min(50.0, (float) $request->input('marketplace_tax_percent', 5.0))),
            'marketplace_listing_fee' => max(0, (int) $request->input('marketplace_listing_fee', 0)),
            'marketplace_allow_zeny' => $request->has('marketplace_allow_zeny') ? '1' : '0',
            'marketplace_allow_cashpoints' => $request->has('marketplace_allow_cashpoints') ? '1' : '0',
            'marketplace_allow_withdrawals' => $request->has('marketplace_allow_withdrawals') ? '1' : '0',
            'marketplace_allow_stackables' => $request->has('marketplace_allow_stackables') ? '1' : '0',
            'marketplace_allow_equipment' => $request->has('marketplace_allow_equipment') ? '1' : '0',
            'marketplace_allow_bound' => $request->has('marketplace_allow_bound') ? '1' : '0',
            'marketplace_allow_rental' => $request->has('marketplace_allow_rental') ? '1' : '0',
            'marketplace_allow_refined' => $request->has('marketplace_allow_refined') ? '1' : '0',
            'marketplace_allow_cards' => $request->has('marketplace_allow_cards') ? '1' : '0',
            'marketplace_history_enabled' => $request->has('marketplace_history_enabled') ? '1' : '0',
        ];

        foreach ($fields as $key => $value) {
            SiteSetting::set($key, (string) $value, 'marketplace');
        }

        ActivityLog::log(
            'admin.marketplace_settings',
            'Updated player marketplace configuration settings.',
            Auth::user()->account_id
        );

        return redirect()->route('admin.marketplace.index', ['tab' => 'settings'])->with('success', 'Marketplace configuration and economy rules updated successfully.');
    }

    public function cancelListing(Request $request, int $id)
    {
        $listing = MarketplaceListing::findOrFail($id);

        if ($listing->status !== 'active') {
            return back()->with('error', 'Only active marketplace listings can be cancelled.');
        }

        $listing->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        if ($listing->item) {
            $listing->item->update(['status' => 'in_custody']);
        }

        ActivityLog::log(
            'admin.marketplace_cancel',
            "Administrator cancelled marketplace listing #{$listing->listing_code} (Item: {$listing->item->formatted_name})",
            Auth::user()->account_id
        );

        return back()->with('success', "Listing [{$listing->listing_code}] cancelled. Item released back to seller's custody inventory.");
    }

    public function returnItem(Request $request, int $id)
    {
        try {
            $item = MarketplaceItem::findOrFail($id);
            $itemName = $item->formatted_name;
            $accountId = $item->account_id;

            $this->escrowService->adminForceWithdrawToStorage($id);

            ActivityLog::log(
                'admin.marketplace_return',
                "Administrator forced return of custody item #{$id} ({$itemName}) to Kafra storage of account #{$accountId}",
                Auth::user()->account_id
            );

            return back()->with('success', "Item [{$itemName}] successfully transferred back to account #{$accountId}'s Kafra storage.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to return item to storage: ' . $e->getMessage());
        }
    }
}
