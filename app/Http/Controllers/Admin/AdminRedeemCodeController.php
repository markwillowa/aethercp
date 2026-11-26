<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\RedeemCampaign;
use App\Models\RedeemCode;
use App\Models\RedeemCodeReward;
use App\Models\RedeemRedemption;
use App\Models\RedeemRewardDelivery;
use App\Models\SiteSetting;
use App\Services\Redeem\RedeemCodeService;
use App\Services\Redeem\RedeemRedemptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRedeemCodeController extends Controller
{
    public function __construct(
        protected RedeemCodeService $codeService,
        protected RedeemRedemptionService $redemptionService
    ) {}

    protected function authorizePerm(string $perm): void
    {
        $user = Auth::user();
        if (!$user || !$user->hasRedeemPermission($perm)) {
            abort(403, "Missing permission [{$perm}].");
        }
    }

    public function index(Request $request)
    {
        $this->authorizePerm('view_dashboard');

        $campaignId = $request->query('campaign_id') ? (int) $request->query('campaign_id') : null;
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $stats = $this->codeService->getDashboardStats($campaignId, $dateFrom, $dateTo);
        $campaigns = RedeemCampaign::withCount(['codes', 'redemptions'])->latest('id')->get();
        $permissions = RedeemCodeService::PERMISSIONS;
        $staffPerms = json_decode((string) SiteSetting::get('redeem_staff_permissions', '[]'), true) ?: [];

        return view('admin.redeem.index', compact(
            'stats',
            'campaigns',
            'campaignId',
            'dateFrom',
            'dateTo',
            'permissions',
            'staffPerms'
        ));
    }

    public function storeCampaign(Request $request)
    {
        $this->authorizePerm('manage_campaigns');

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'banner_url' => 'nullable|string|max:500',
            'campaign_type' => 'required|string|max:40',
            'instructions' => 'nullable|string|max:2000',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'status' => 'required|in:draft,scheduled,active,ended,disabled',
            'max_total_redemptions' => 'nullable|integer|min:1',
            'max_per_account' => 'nullable|integer|min:1',
            'account_age_rule' => 'nullable|in:any,new_only,existing_only',
            'min_account_age_days' => 'nullable|integer|min:0',
            'min_base_level' => 'nullable|integer|min:1|max:9999',
            'max_base_level' => 'nullable|integer|min:1|max:9999',
            'allowed_account_ids_raw' => 'nullable|string|max:2000',
            'allowed_character_ids_raw' => 'nullable|string|max:2000',
            'allowed_classes_raw' => 'nullable|string|max:1000',
            'excluded_classes_raw' => 'nullable|string|max:1000',
        ]);

        $parseIds = static function (?string $raw): ?array {
            $ids = array_values(array_filter(array_map(fn ($v) => (int) trim($v), explode(',', (string) $raw)), fn ($v) => $v > 0));
            return empty($ids) ? null : $ids;
        };

        $campaign = $this->codeService->createCampaign(array_merge($validated, [
            'is_public' => $request->has('is_public'),
            'one_per_account' => $request->has('one_per_account'),
            'one_per_character' => $request->has('one_per_character'),
            'allowed_account_ids' => $parseIds($validated['allowed_account_ids_raw'] ?? null),
            'allowed_character_ids' => $parseIds($validated['allowed_character_ids_raw'] ?? null),
            'allowed_classes' => $parseIds($validated['allowed_classes_raw'] ?? null),
            'excluded_classes' => $parseIds($validated['excluded_classes_raw'] ?? null),
        ]), Auth::user());

        return redirect()->route('admin.redeem.show', $campaign->id)
            ->with('success', "Campaign [{$campaign->name}] created.");
    }

    public function showCampaign(int $id)
    {
        $this->authorizePerm('view_dashboard');

        $campaign = RedeemCampaign::with(['rewards', 'codes' => fn ($q) => $q->latest('id')])->findOrFail($id);
        $logs = $campaign->logs()->with('actor')->latest('id')->limit(30)->get();

        return view('admin.redeem.show', compact('campaign', 'logs'));
    }

    public function updateCampaign(Request $request, int $id)
    {
        $this->authorizePerm('manage_campaigns');

        $campaign = RedeemCampaign::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'banner_url' => 'nullable|string|max:500',
            'campaign_type' => 'required|string|max:40',
            'instructions' => 'nullable|string|max:2000',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'status' => 'required|in:draft,scheduled,active,ended,disabled',
            'max_total_redemptions' => 'nullable|integer|min:1',
            'max_per_account' => 'nullable|integer|min:1',
            'min_base_level' => 'nullable|integer|min:1|max:9999',
            'max_base_level' => 'nullable|integer|min:1|max:9999',
        ]);

        $this->codeService->updateCampaign($campaign, array_merge($validated, [
            'is_public' => $request->has('is_public'),
            'one_per_account' => $request->has('one_per_account'),
            'one_per_character' => $request->has('one_per_character'),
        ]), Auth::user());

        return back()->with('success', 'Campaign updated.');
    }

    public function generateCodes(Request $request, int $id)
    {
        $this->authorizePerm('generate_codes');

        $campaign = RedeemCampaign::findOrFail($id);
        $validated = $request->validate([
            'mode' => 'required|in:single_manual,single_auto,bulk,shared',
            'manual_code' => 'nullable|string|max:64',
            'prefix' => 'nullable|string|max:20',
            'length' => 'nullable|integer|min:4|max:24',
            'charset' => 'nullable|in:alphanumeric_safe,alphanumeric,alpha,numeric',
            'count' => 'nullable|integer|min:1|max:1000',
            'max_redemptions' => 'nullable|integer|min:1|max:1000000',
            'expires_at' => 'nullable|date',
            'bound_account_id' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,disabled',
        ]);

        try {
            $result = $this->codeService->generateCodes($campaign, $validated, Auth::user());

            // Store one-time plaintext export token in session for immediate CSV download
            session()->flash('generated_codes_plaintext', $result['plaintext']);
            session()->put('redeem_one_time_export_' . $campaign->id, $result['plaintext']);

            return back()->with('success', 'Generated ' . count($result['codes']) . ' code(s). Copy or export them now — plaintext codes are not stored in the database.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportGeneratedCsv(int $id)
    {
        $this->authorizePerm('export_codes');

        $campaign = RedeemCampaign::findOrFail($id);
        $codes = session()->pull('redeem_one_time_export_' . $campaign->id, []);
        if (empty($codes)) {
            return back()->with('error', 'No one-time generated codes available for export. Generate a batch first.');
        }

        $csv = "Code,Campaign\n";
        foreach ($codes as $code) {
            $csv .= '"' . str_replace('"', '""', $code) . '","' . str_replace('"', '""', $campaign->name) . "\"\n";
        }

        $this->codeService->log(Auth::user(), 'codes.exported', "Exported " . count($codes) . " generated code(s) to CSV", $campaign->id);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="redeem-codes-' . $campaign->slug . '.csv"',
        ]);
    }

    public function toggleCode(int $codeId)
    {
        $this->authorizePerm('disable_codes');

        $code = RedeemCode::findOrFail($codeId);
        $newStatus = $code->status === 'disabled' ? 'active' : 'disabled';
        $code->update(['status' => $newStatus]);

        $this->codeService->log(Auth::user(), 'code.toggled', "Set code {$code->code_hint} status to [{$newStatus}]", $code->campaign_id, $code->id);
        ActivityLog::log('redeem.code_toggle', "Set redeem code {$code->code_hint} to {$newStatus}.");

        return back()->with('success', "Code {$code->code_hint} is now {$newStatus}.");
    }

    public function addReward(Request $request, int $id)
    {
        $this->authorizePerm('manage_rewards');

        $campaign = RedeemCampaign::findOrFail($id);
        $validated = $request->validate([
            'reward_type' => 'required|in:cash_points,zeny,item,custom',
            'amount' => 'required|integer|min:1|max:2147483647',
            'delivery_scope' => 'nullable|in:account,character,escrow',
            'item_id' => 'nullable|integer|min:1',
            'refine' => 'nullable|integer|min:0|max:20',
            'card0' => 'nullable|integer|min:0',
            'card1' => 'nullable|integer|min:0',
            'card2' => 'nullable|integer|min:0',
            'card3' => 'nullable|integer|min:0',
            'custom_label' => 'nullable|string|max:120',
        ]);

        try {
            $this->codeService->addReward($campaign, array_merge($validated, [
                'delivery_scope' => $validated['reward_type'] === 'zeny' ? 'escrow' : ($validated['delivery_scope'] ?? 'account'),
                'is_bound' => $request->has('is_bound'),
                'custom_payload' => $validated['reward_type'] === 'custom' ? ['label' => $validated['custom_label'] ?? 'Custom Reward'] : null,
            ]), Auth::user());

            return back()->with('success', 'Reward added to campaign.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteReward(int $rewardId)
    {
        $this->authorizePerm('manage_rewards');

        $reward = RedeemCodeReward::findOrFail($rewardId);
        $reward->delete();

        return back()->with('success', 'Reward removed.');
    }

    public function logs(Request $request)
    {
        $this->authorizePerm('view_history');

        $query = RedeemRedemption::with(['campaign', 'deliveries'])->latest('id');

        if ($request->filled('search')) {
            $s = trim((string) $request->query('search'));
            $query->where(function ($q) use ($s) {
                $q->where('code_hint', 'like', "%{$s}%")
                  ->orWhere('username_snapshot', 'like', "%{$s}%")
                  ->orWhere('account_id', $s)
                  ->orWhere('character_name_snapshot', 'like', "%{$s}%");
            });
        }
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', (int) $request->query('campaign_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->query('date_from') . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->query('date_to') . ' 23:59:59');
        }

        $redemptions = $query->paginate(25)->withQueryString();
        $campaigns = RedeemCampaign::orderBy('name')->get(['id', 'name']);

        return view('admin.redeem.logs', compact('redemptions', 'campaigns'));
    }

    public function exportLogs(Request $request)
    {
        $this->authorizePerm('export_logs');

        $rows = RedeemRedemption::with('campaign')->latest('id')->limit(1000)->get();
        $csv = "ID,Code,Campaign,Account ID,Username,Character,Status,Delivery Status,IP,Redeemed At\n";
        foreach ($rows as $r) {
            $csv .= implode(',', [
                $r->id,
                '"' . str_replace('"', '""', $r->code_hint) . '"',
                '"' . str_replace('"', '""', $r->campaign?->name ?? '') . '"',
                $r->account_id,
                '"' . str_replace('"', '""', $r->username_snapshot ?? '') . '"',
                '"' . str_replace('"', '""', $r->character_name_snapshot ?? '') . '"',
                $r->status,
                $r->delivery_status,
                $r->ip_address ?? '',
                $r->redeemed_at?->toDateTimeString() ?? '',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="redeem-redemption-logs.csv"',
        ]);
    }

    public function retryDelivery(int $deliveryId)
    {
        $this->authorizePerm('retry_deliveries');

        $delivery = RedeemRewardDelivery::with('redemption')->findOrFail($deliveryId);
        $ok = $this->redemptionService->retryDelivery($delivery, Auth::user());

        return back()->with($ok ? 'success' : 'error', $ok ? "Delivery #{$deliveryId} succeeded!" : "Delivery #{$deliveryId} failed: " . ($delivery->fresh()->error_message ?? 'Unknown error'));
    }

    public function updatePermissions(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }

        $perms = array_values(array_intersect(
            array_keys(RedeemCodeService::PERMISSIONS),
            $request->input('permissions', [])
        ));

        SiteSetting::set('redeem_staff_permissions', json_encode($perms), 'redeem_codes');
        ActivityLog::log('redeem.permissions_update', 'Updated Redeem Codes staff permissions.');

        return back()->with('success', 'Redeem Codes staff permissions updated.');
    }
}
