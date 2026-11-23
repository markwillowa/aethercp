<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DonationPackage;
use App\Models\Donation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function index()
    {
        $packages = DonationPackage::active()->get();
        $userDonations = Auth::check()
            ? Donation::where('account_id', Auth::user()->account_id)->with('package')->latest()->get()
            : collect();

        return view('donations.index', compact('packages', 'userDonations'));
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
}

