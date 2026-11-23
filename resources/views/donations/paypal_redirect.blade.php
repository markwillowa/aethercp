@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12 px-4 text-center">
    <div class="ro-panel p-8 bg-[#FCF7ED] border border-[#DECBB1] shadow-2xl space-y-5">
        <div class="w-16 h-16 rounded-2xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center mx-auto text-blue-500 animate-pulse">
            <i data-lucide="shield-check" class="w-8 h-8"></i>
        </div>

        <div>
            <h1 class="text-xl font-bold font-serif text-[#2D1D13]">Connecting to PayPal</h1>
            <p class="text-xs text-[#5C4632] mt-1">Please wait while we transfer you securely to PayPal checkout...</p>
        </div>

        <div class="p-3 bg-[#F5EBD5] border border-[#DECBB1] rounded text-xs text-left space-y-1">
            <div class="flex justify-between">
                <span class="text-[#77644E]">Package:</span>
                <strong class="text-[#3A2A1C]">{{ $package->name }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-[#77644E]">Cash Points:</span>
                <strong class="text-[#2E7D32]">{{ number_format($package->points + $package->bonus_points) }} pts</strong>
            </div>
            @if($package->item_amount > 0 && $package->item_name)
                <div class="flex justify-between">
                    <span class="text-[#77644E]">Bonus Item:</span>
                    <strong class="text-[#8C5E1E]">{{ $package->item_amount }}x {{ $package->item_name }}</strong>
                </div>
            @endif
            <div class="flex justify-between pt-1 border-t border-[#DECBB1] font-bold">
                <span class="text-[#3A2A1C]">Total Price:</span>
                <strong class="text-[#315B88]">{{ $params['currency_code'] }} ${{ $params['amount'] }}</strong>
            </div>
        </div>

        <!-- Hidden PayPal Auto-Submit Form -->
        <form id="paypal_form" method="POST" action="{{ $paypalUrl }}">
            @foreach($params as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach

            <button type="submit" class="ro-btn-primary w-full py-2.5 text-xs">
                <span>Click here if you are not redirected automatically</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </button>
        </form>

        <a href="{{ route('donations.index') }}" class="block text-xs text-[#77644E] hover:underline">
            Cancel and Return to Server
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            var form = document.getElementById('paypal_form');
            if (form) form.submit();
        }, 800);
    });
</script>
@endsection

