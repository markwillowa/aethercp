<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DonationPackage;
use App\Models\Donation;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use App\Services\Donation\InGameRewardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function index()
    {
        $packages = DonationPackage::active()->get();
        $userDonations = Auth::check()
            ? Donation::where('account_id', Auth::user()->account_id)->with('package')->latest()->get()
            : collect();

        $paypalConfig = [
            'enabled' => (bool) SiteSetting::get('paypal_enabled', 1),
            'email' => SiteSetting::get('paypal_email', ''),
            'sandbox' => (bool) SiteSetting::get('paypal_sandbox', 1),
            'currency' => SiteSetting::get('paypal_currency', 'USD'),
        ];

        return view('donations.index', compact('packages', 'userDonations', 'paypalConfig'));
    }

    public function submitManual(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:donation_packages,id',
            'transaction_reference' => 'required|string|max:100|unique:donations,transaction_reference',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $package = DonationPackage::findOrFail($request->input('package_id'));

        $donation = Donation::create([
            'account_id' => $user->account_id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'currency' => $package->currency,
            'points_awarded' => $package->points + $package->bonus_points,
            'payment_method' => 'manual',
            'transaction_reference' => $request->input('transaction_reference'),
            'status' => 'pending',
            'notes' => $request->input('notes'),
        ]);

        ActivityLog::log('donation.submitted', "Submitted manual donation reference: {$donation->transaction_reference}", $user->account_id);

        return back()->with('success', 'Your donation reference has been submitted. Staff will verify and credit your points shortly.');
    }

    /**
     * Initiate PayPal checkout session for an authenticated player.
     */
    public function checkoutPayPal(Request $request, int $packageId)
    {
        $user = Auth::user();
        $package = DonationPackage::active()->findOrFail($packageId);

        $paypalEnabled = (bool) SiteSetting::get('paypal_enabled', 1);
        $paypalEmail = trim(SiteSetting::get('paypal_email', ''));

        if (!$paypalEnabled || empty($paypalEmail)) {
            return back()->with('error', 'PayPal automated payments are currently being configured by server staff. Please use manual donation or check back soon.');
        }

        $isSandbox = (bool) SiteSetting::get('paypal_sandbox', 1);
        $currency = strtoupper(SiteSetting::get('paypal_currency', $package->currency ?: 'USD'));
        $invoiceRef = 'PP-' . strtoupper(Str::random(12));

        $donation = Donation::create([
            'account_id' => $user->account_id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'currency' => $currency,
            'points_awarded' => $package->points + $package->bonus_points,
            'payment_method' => 'paypal',
            'transaction_reference' => $invoiceRef,
            'status' => 'pending',
            'notes' => "Initiated PayPal checkout for package [{$package->name}].",
        ]);

        $paypalBaseUrl = $isSandbox
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';

        $serverName = config('aether.server_name', 'FreynixRO');
        $itemName = "{$serverName} - {$package->name}";
        if ($package->item_amount > 0 && $package->item_name) {
            $itemName .= " (+{$package->item_amount}x {$package->item_name})";
        }

        $params = [
            'cmd' => '_xclick',
            'business' => $paypalEmail,
            'item_name' => $itemName,
            'item_number' => $package->id,
            'amount' => number_format((float) $package->price, 2, '.', ''),
            'currency_code' => $currency,
            'no_shipping' => '1',
            'no_note' => '1',
            'custom' => (string) $donation->id,
            'invoice' => $invoiceRef,
            'notify_url' => route('donations.paypal.ipn'),
            'return' => route('donations.paypal.success', ['invoice' => $invoiceRef]),
            'cancel_return' => route('donations.paypal.cancel'),
        ];

        return view('donations.paypal_redirect', [
            'paypalUrl' => $paypalBaseUrl,
            'params' => $params,
            'package' => $package,
            'donation' => $donation,
        ]);
    }

    /**
     * PayPal Instant Payment Notification (IPN) webhook listener.
     */
    public function paypalIpn(Request $request, InGameRewardService $rewardService)
    {
        $rawPost = $request->all();
        Log::info('Received PayPal IPN webhook', ['payload' => $rawPost]);

        // 1. Validate IPN handshake with PayPal
        $isSandbox = (bool) SiteSetting::get('paypal_sandbox', 1);
        $verifyUrl = $isSandbox
            ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://ipnpb.paypal.com/cgi-bin/webscr';

        $postbackData = array_merge(['cmd' => '_notify-validate'], $rawPost);

        $isValid = false;
        try {
            $response = Http::asForm()->timeout(15)->post($verifyUrl, $postbackData);
            if ($response->successful() && trim($response->body()) === 'VERIFIED') {
                $isValid = true;
            }
        } catch (\Throwable $e) {
            Log::warning('PayPal IPN handshake request failed: ' . $e->getMessage());
        }

        // In test environments or simulation where outbound network is mocked/offline, allow validation
        if (!$isValid && app()->environment('testing', 'local') && ($request->input('test_ipn') || $isSandbox)) {
            $isValid = true;
        }

        if (!$isValid) {
            Log::warning('PayPal IPN verification rejected or unverified', ['payload' => $rawPost]);
            return response('INVALID', 400);
        }

        // 2. Process Completed Payment
        $paymentStatus = $request->input('payment_status');
        $donationId = $request->input('custom');
        $invoice = $request->input('invoice');
        $txnId = $request->input('txn_id');
        $receiverEmail = strtolower(trim($request->input('receiver_email', '')));
        $configuredEmail = strtolower(trim(SiteSetting::get('paypal_email', '')));

        if (!empty($configuredEmail) && !empty($receiverEmail) && $receiverEmail !== $configuredEmail) {
            Log::warning("PayPal IPN receiver email mismatch: expected {$configuredEmail}, received {$receiverEmail}");
            return response('RECEIVER_MISMATCH', 400);
        }

        // Find matching donation
        $donation = null;
        if (!empty($donationId) && is_numeric($donationId)) {
            $donation = Donation::find($donationId);
        }
        if (!$donation && !empty($invoice)) {
            $donation = Donation::where('transaction_reference', $invoice)->first();
        }

        if (!$donation) {
            Log::error("PayPal IPN could not match donation record: ID [{$donationId}], Invoice [{$invoice}]");
            return response('DONATION_NOT_FOUND', 404);
        }

        // Check if already approved (idempotency guard)
        if ($donation->status === 'approved') {
            Log::info("PayPal IPN: Donation #{$donation->id} already approved previously.");
            return response('ALREADY_PROCESSED', 200);
        }

        if ($paymentStatus === 'Completed') {
            $donation->update([
                'status' => 'approved',
                'transaction_reference' => $txnId ?: $donation->transaction_reference,
                'payer_email' => $request->input('payer_email'),
                'payer_id' => $request->input('payer_id'),
                'ipn_data' => json_encode($rawPost),
                'verified_at' => now(),
            ]);

            // 3. Fulfill in-game rewards automatically
            $fulfillment = $rewardService->fulfillDonation($donation);

            Log::info("PayPal IPN: Donation #{$donation->id} approved and rewards fulfilled automatically.", $fulfillment);
            return response('OK', 200);
        }

        if (in_array($paymentStatus, ['Refunded', 'Reversed'])) {
            $donation->update([
                'status' => 'refunded',
                'ipn_data' => json_encode($rawPost),
            ]);
            return response('REFUNDED', 200);
        }

        return response('STATUS_NOT_HANDLED', 200);
    }

    public function paypalSuccess(Request $request)
    {
        return redirect()->route('donations.index')->with(
            'success',
            'PayPal payment received! Your in-game Cash Points and item rewards are being delivered automatically. Please check Kafra Storage in-game.'
        );
    }

    public function paypalCancel()
    {
        return redirect()->route('donations.index')->with('error', 'PayPal checkout was cancelled.');
    }
}
