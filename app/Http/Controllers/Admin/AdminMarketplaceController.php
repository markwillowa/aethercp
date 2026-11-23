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

        // 1. Statistics
        $stats = [
            'active_listings' => MarketplaceListing::where('status', 'active')->where('expires_at', '>', now())->count(),
            'total_items_in_custody' => MarketplaceItem::where('status', 'in_custody')->count(),
            'total_sales_count' => MarketplaceTransaction::where('status', 'completed')->count(),
            'zeny_volume' => MarketplaceTransaction::where('status', 'completed')->where('currency_type', 'zeny')->sum('gross_amount'),
            'zeny_tax_collected' => MarketplaceTransaction::where('status', 'completed')->where('currency_type', 'zeny')->sum('tax_amount'),
            'points_volume' => MarketplaceTransaction::where('status', 'completed')->where('currency_type', 'cashpoints')->sum('gross_amount'),
            'points_tax_collected' => MarketplaceTransaction::where('status', 'completed')->where('currency_type', 'cashpoints')->sum('tax_amount'),
        ];

        // 2. Listings with filters
        $listingsQuery = MarketplaceListing::with(['item', 'sellerUser'])->latest();
        if ($filterStatus = $request->query('status')) {
            if ($filterStatus !== 'all') {
                $listingsQuery->where('status', $filterStatus);
            }
        }
        $listings = $listingsQuery->paginate(20, ['*'], 'listings_page')->withQueryString();

        // 3. Transactions Audit Log
        $transactions = MarketplaceTransaction::with(['item', 'sellerUser', 'buyerUser'])
            ->latest()
            ->paginate(20, ['*'], 'tx_page')
            ->withQueryString();

        return view('admin.marketplace.index', compact('settings', 'stats', 'listings', 'transactions'));
    }

    public function updateSettings(Request $request)
    {
        $fields = [
            'marketplace_maintenance_mode' => $request->has('marketplace_maintenance_mode') ? '1' : '0',
            'marketplace_announcement' => $request->input('marketplace_announcement', ''),
            'marketplace_rules' => $request->input('marketplace_rules', ''),
            'marketplace_max_listings_player' => (int) $request->input('marketplace_max_listings_player', 15),
            'marketplace_max_listings_char' => (int) $request->input('marketplace_max_listings_char', 8),
            'marketplace_min_price' => (int) $request->input('marketplace_min_price', 1),
            'marketplace_max_price' => (int) $request->input('marketplace_max_price', 1000000000),
            'marketplace_default_duration_hours' => (int) $request->input('marketplace_default_duration_hours', 72),
            'marketplace_tax_percent' => (float) $request->input('marketplace_tax_percent', 5.0),
            'marketplace_listing_fee' => (int) $request->input('marketplace_listing_fee', 0),
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

        return back()->with('success', 'Marketplace configuration updated successfully.');
    }

    public function cancelListing(Request $request, int $id)
    {
        $listing = MarketplaceListing::findOrFail($id);

        if ($listing->status !== 'active') {
            return back()->with('error', 'Only active listings can be cancelled.');
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
}
