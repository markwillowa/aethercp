@extends('layouts.app')

@section('content')
@php
    $initialPkg = $packages->firstWhere('is_popular', true) ?? $packages->first();
    $packagesData = $packages->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'description' => $p->description,
        'price' => (float) $p->price,
        'price_formatted' => number_format((float) $p->price, 2),
        'currency' => $p->currency,
        'points' => (int) $p->points,
        'points_formatted' => number_format((int) $p->points),
        'bonus_points' => (int) $p->bonus_points,
        'bonus_points_formatted' => number_format((int) $p->bonus_points),
        'total_points' => (int) ($p->points + $p->bonus_points),
        'total_points_formatted' => number_format((int) ($p->points + $p->bonus_points)),
        'item_id' => $p->item_id,
        'item_name' => $p->item_name,
        'item_amount' => (int) $p->item_amount,
        'is_popular' => (bool) $p->is_popular,
        'checkout_url' => route('donations.paypal.checkout', $p->id),
    ])->values();
@endphp

<div x-data="{
    packages: {{ $packagesData->toJson() }},
    selectedId: {{ $initialPkg ? $initialPkg->id : 'null' }},
    paymentMode: 'paypal',
    get current() { return this.packages.find(p => p.id === this.selectedId) || this.packages[0] || {}; }
}" class="payon-container py-10 space-y-8">

    <div class="payon-wood-frame overflow-hidden">
        <div class="payon-wood-header">
            <div class="flex items-center gap-2">
                <i data-lucide="gem" class="w-4 h-4 text-[#A88950]"></i>
                <span class="font-cinzel uppercase tracking-wider text-sm">Payon Royal Treasury &amp; Tribute</span>
            </div>
            @auth
                <span class="text-[11px] font-mono text-[#E7D8B5]">Account: {{ auth()->user()->username ?? auth()->user()->name }}</span>
            @endauth
        </div>
        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">Kingdom Donations &amp; Cash Points</h1>
                <p class="text-xs text-[#80633B] mt-1">Acquire Cash Points and bonus treasure items with instant account fulfillment.</p>
            </div>
        </div>
        @if(!empty($donationSettings['instructions']))
            <div class="px-6 py-3 border-t border-[#A88950]/40 bg-[#F2E8CF] text-xs text-[#493322]">
                <strong class="font-cinzel">Patronage Notice:</strong> {{ $donationSettings['instructions'] }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-7 space-y-3">
            @foreach($packages as $idx => $pkg)
                <div @click="selectedId = {{ $pkg->id }}"
                     :class="selectedId === {{ $pkg->id }} ? 'border-2 border-[#263D2C] bg-[#FFF8E8] shadow-lg' : 'border border-[#A88950] bg-[#F8F0DD] hover:bg-[#FFF8E8]'"
                     class="rounded-lg p-5 cursor-pointer transition-all flex items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h3 class="font-cinzel text-base font-bold text-[#35291F]">{{ $pkg->name }}</h3>
                            @if($pkg->is_popular)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#263D2C] text-[#FFF8E8]">Most Favored</span>
                            @endif
                        </div>
                        <p class="text-xs text-[#80633B]">{{ $pkg->description }}</p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-[#E7D8B5] text-[#35291F] border border-[#A88950]">{{ number_format($pkg->points) }} Points</span>
                            @if($pkg->bonus_points > 0)
                                <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-[#526D3A]/15 text-[#263D2C] border border-[#526D3A]/40">+{{ number_format($pkg->bonus_points) }} Bonus</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="font-cinzel text-2xl font-extrabold text-[#263D2C] block">${{ number_format($pkg->price, 2) }}</span>
                        <span class="text-[10px] font-mono uppercase text-[#80633B]">{{ $pkg->currency }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="lg:col-span-5 payon-scroll-panel p-6 space-y-5 sticky top-24">
            <h2 class="font-cinzel text-lg font-bold text-[#35291F] border-b border-[#A88950]/40 pb-3">Treasury Checkout</h2>

            <div class="p-4 rounded bg-[#E7D8B5] border border-[#A88950] space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-[#80633B]">Selected Tier:</span><strong class="font-cinzel text-[#35291F]" x-text="current.name"></strong></div>
                <div class="flex justify-between"><span class="text-[#80633B]">Total Points:</span><strong class="font-mono text-[#263D2C]" x-text="(current.total_points_formatted || '0') + ' PTS'"></strong></div>
                <div class="flex justify-between border-t border-[#A88950]/40 pt-2"><span class="font-bold">Total:</span><strong class="font-cinzel text-lg text-[#263D2C]" x-text="'$' + (current.price_formatted || '0.00')"></strong></div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" @click="paymentMode = 'paypal'" :class="paymentMode === 'paypal' ? 'payon-btn-forest' : 'payon-btn-wood opacity-75'">Instant PayPal</button>
                <button type="button" @click="paymentMode = 'manual'" :class="paymentMode === 'manual' ? 'payon-btn-forest' : 'payon-btn-wood opacity-75'">Manual / Offline</button>
            </div>

            <div x-show="paymentMode === 'paypal'">
                @auth
                    @if(!empty($paypalConfig['enabled']) && !empty($paypalConfig['email']))
                        <form method="POST" :action="current.checkout_url">
                            @csrf
                            <button type="submit" class="payon-btn-gold w-full py-3">Instant PayPal Checkout</button>
                        </form>
                    @else
                        <div class="p-3 rounded bg-[#E7D8B5] text-xs text-center text-[#493322]">PayPal Gateway Under Configuration. Use Manual / Offline tab.</div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="payon-btn-forest w-full py-3">Sign In to Acquire Points</a>
                @endauth
            </div>

            <div x-show="paymentMode === 'manual'" style="display: none;" class="space-y-3">
                @if(!empty($donationSettings['payment_details']))
                    <div class="p-3 rounded bg-[#E7D8B5] border border-[#A88950] text-[11px] text-[#493322] whitespace-pre-line">{{ $donationSettings['payment_details'] }}</div>
                @endif
                @auth
                    <form method="POST" action="{{ route('donations.submit') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="package_id" :value="current.id">
                        <input type="text" name="transaction_reference" required placeholder="Transaction Reference / Receipt ID" class="w-full px-3 py-2 rounded text-xs">
                        <textarea name="notes" rows="2" placeholder="Additional notes..." class="w-full px-3 py-2 rounded text-xs"></textarea>
                        <button type="submit" class="payon-btn-gold w-full py-2.5">Submit Claim for Verification</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="payon-btn-forest w-full py-2.5">Sign In to Submit Claim</a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
