@extends('layouts.app')

@section('content')
@php
    $initialPkg = $packages->firstWhere('is_popular', true) ?? $packages->first();
    $packagesData = $packages->map(function($p) {
        return [
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
        ];
    })->values();
@endphp

<div x-data="{
    packages: {{ $packagesData->toJson() }},
    selectedId: {{ $initialPkg ? $initialPkg->id : 'null' }},
    paymentMode: 'paypal',
    get current() {
        return this.packages.find(p => p.id === this.selectedId) || this.packages[0] || {};
    }
}" class="prestige-container py-10 space-y-10">

    <!-- ======================================================================
         TOP VAULT HERO STRIP (FAQ-Style Centered Hero)
         ====================================================================== -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-[#151B26] to-[#101725] border border-[#C6A76A]/30 p-6 sm:p-10 shadow-2xl">
        <div class="relative z-10 text-center max-w-2xl mx-auto space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C6A76A]/10 border border-[#C6A76A]/30 text-[#D8BE8A] text-[10px] font-bold uppercase tracking-luxury font-cinzel">
                <i data-lucide="crown" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Sovereign Treasury &amp; Patronage Vault</span>
            </div>
            <h1 class="font-cinzel text-2xl sm:text-4xl font-extrabold text-white tracking-wider uppercase">
                Acquire Cash Shop Points
            </h1>
            <p class="text-xs sm:text-sm text-[#AAB3C1] leading-relaxed">
                Directly empower high-frequency dedicated server infrastructure, anti-cheat licensing, and content expansions. Cash Points (#CASHPOINTS) and complimentary bonus items deliver in real time.
            </p>
            @auth
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-xl bg-[#101725]/80 border border-white/[0.08] text-xs mt-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[#AAB3C1]">Account: <strong class="font-cinzel text-white">{{ auth()->user()->username ?? auth()->user()->name }}</strong></span>
                    <span class="text-[#C6A76A] font-mono">• Instant Fulfillment Ready</span>
                </div>
            @endauth
        </div>
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-48 bg-[#C6A76A]/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Instructions Notice (if present) -->
    @if(!empty($donationSettings['instructions']))
        <div class="p-4 rounded-xl bg-[#151B26] border border-white/[0.08] text-xs text-[#AAB3C1] flex items-start gap-3.5 shadow-md">
            <i data-lucide="info" class="w-5 h-5 text-[#C6A76A] shrink-0 mt-0.5"></i>
            <div>
                <strong class="text-white font-cinzel text-xs font-bold block mb-0.5">Patronage Notice:</strong>
                <p class="whitespace-pre-line leading-relaxed text-[#AAB3C1]">{{ $donationSettings['instructions'] }}</p>
            </div>
        </div>
    @endif

    <!-- ======================================================================
         2-COLUMN INTERACTIVE CONSOLE (Slabs Left, Terminal Right)
         ====================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN (7 Cols): Interactive Tier Slabs -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between border-b border-white/[0.08] pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4 text-[#C6A76A]"></i>
                    <h2 class="font-cinzel text-sm font-bold uppercase tracking-wider text-white">Select Patronage Tier</h2>
                </div>
                <span class="text-[11px] text-[#788394] font-mono">{{ count($packages) }} Tiers Available</span>
            </div>

            <div class="space-y-3">
                @php
                    $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
                @endphp
                @foreach($packages as $idx => $pkg)
                    <div @click="selectedId = {{ $pkg->id }}"
                         :class="selectedId === {{ $pkg->id }} 
                            ? 'border-[#C6A76A] bg-[#1B2432] shadow-xl shadow-[#C6A76A]/10 ring-1 ring-[#C6A76A]/50' 
                            : 'border-white/[0.08] bg-[#151B26]/80 hover:border-white/20 hover:bg-[#151B26]'"
                         class="cursor-pointer rounded-2xl border p-5 transition-all duration-200 relative overflow-hidden group">
                        
                        @if($pkg->is_popular)
                            <div class="absolute -top-px -right-px bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] text-[9px] font-black uppercase tracking-widest px-3 py-0.5 rounded-bl-lg font-cinzel shadow-sm">
                                Most Favored
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <!-- Tier Emblems & Title -->
                            <div class="flex items-start gap-4">
                                <div :class="selectedId === {{ $pkg->id }} ? 'bg-[#C6A76A] text-[#101725]' : 'bg-[#101725] text-[#AAB3C1] border border-white/10'"
                                     class="w-10 h-10 rounded-xl flex items-center justify-center font-cinzel font-black text-sm shrink-0 transition-colors shadow-inner">
                                    {{ $romanNumerals[$idx % count($romanNumerals)] }}
                                </div>

                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-cinzel text-base font-bold text-white group-hover:text-[#D8BE8A] transition-colors">
                                            {{ $pkg->name }}
                                        </h3>
                                        <span class="text-[10px] text-[#788394] uppercase font-mono">Tier {{ $idx + 1 }}</span>
                                    </div>
                                    <p class="text-xs text-[#788394] line-clamp-1">{{ $pkg->description }}</p>

                                    <!-- Points & Bonus Badges -->
                                    <div class="flex flex-wrap items-center gap-2 pt-1.5">
                                        <span class="px-2.5 py-0.5 rounded-md bg-[#101725] border border-white/[0.06] text-xs font-mono font-bold text-[#C6A76A]">
                                            {{ number_format($pkg->points) }} Points
                                        </span>

                                        @if($pkg->bonus_points > 0)
                                            <span class="px-2 py-0.5 rounded-md bg-teal-500/10 border border-teal-500/20 text-[11px] font-mono font-bold text-[#2DD4BF]">
                                                +{{ number_format($pkg->bonus_points) }} Bonus
                                            </span>
                                        @endif

                                        @if($pkg->item_amount > 0 && ($pkg->item_name || $pkg->item_id))
                                            <span class="px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/20 text-[11px] font-cinzel font-bold text-amber-300 flex items-center gap-1">
                                                <i data-lucide="gift" class="w-3 h-3 text-amber-400"></i>
                                                <span>+{{ $pkg->item_amount }}x {{ $pkg->item_name ?: 'Bonus Item' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Price & Select Radio -->
                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 border-white/[0.06] pt-3 sm:pt-0 shrink-0">
                                <div class="text-left sm:text-right">
                                    <span class="font-cinzel text-2xl font-black text-white">${{ number_format($pkg->price, 2) }}</span>
                                    <span class="text-[10px] text-[#788394] uppercase block">{{ $pkg->currency }}</span>
                                </div>

                                <div class="mt-2 flex items-center gap-1.5 text-xs">
                                    <div :class="selectedId === {{ $pkg->id }} ? 'bg-[#C6A76A] border-[#C6A76A]' : 'border-white/20 bg-[#101725]'"
                                         class="w-4 h-4 rounded-full border flex items-center justify-center transition-colors">
                                        <div x-show="selectedId === {{ $pkg->id }}" class="w-1.5 h-1.5 rounded-full bg-[#101725]"></div>
                                    </div>
                                    <span :class="selectedId === {{ $pkg->id }} ? 'text-[#D8BE8A] font-bold' : 'text-[#788394]'" class="text-[11px] uppercase tracking-wider font-cinzel">
                                        <span x-text="selectedId === {{ $pkg->id }} ? 'Selected' : 'Select'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RIGHT COLUMN (5 Cols): Sticky Checkout & Fulfillment Terminal -->
        <div class="lg:col-span-5 sticky top-24 space-y-6">
            <div class="prestige-card-elevated p-6 sm:p-7 shadow-2xl border-[#C6A76A]/40 space-y-6">
                
                <!-- Terminal Header -->
                <div class="border-b border-white/[0.08] pb-4 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-luxury text-[#C6A76A] font-cinzel block">Fulfillment Terminal</span>
                        <h3 class="font-cinzel text-lg font-bold text-white uppercase tracking-wider">Treasury Checkout</h3>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-[#101725] border border-white/10 flex items-center justify-center text-[#C6A76A]">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                </div>

                <!-- Live Summary Display -->
                <div class="bg-[#101725] border border-[#C6A76A]/30 rounded-xl p-5 space-y-4 shadow-inner">
                    <div class="flex items-center justify-between text-xs border-b border-white/[0.06] pb-3">
                        <span class="text-[#788394] uppercase tracking-wider font-cinzel">Selected Tier:</span>
                        <strong class="font-cinzel text-white text-sm" x-text="current.name || 'Select a Tier'"></strong>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[#AAB3C1]">Base Cash Points:</span>
                            <span class="font-mono text-white font-bold" x-text="current.points_formatted || '0'"></span>
                        </div>
                        <template x-if="current.bonus_points > 0">
                            <div class="flex items-center justify-between text-xs text-[#2DD4BF]">
                                <span>Complimentary Bonus:</span>
                                <span class="font-mono font-bold" x-text="'+' + current.bonus_points_formatted"></span>
                            </div>
                        </template>
                        <div class="flex items-center justify-between border-t border-white/[0.06] pt-2 text-sm">
                            <span class="font-cinzel font-bold text-white uppercase tracking-wider">Total Points Delivered:</span>
                            <span class="font-mono font-black text-[#D8BE8A] text-base" x-text="(current.total_points_formatted || '0') + ' PTS'"></span>
                        </div>
                    </div>

                    <template x-if="current.item_amount > 0 && current.item_name">
                        <div class="p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-200 text-xs flex items-center gap-2">
                            <i data-lucide="gift" class="w-4 h-4 text-amber-400 shrink-0"></i>
                            <div>
                                <span class="font-bold font-cinzel" x-text="'+' + current.item_amount + 'x ' + current.item_name"></span>
                                <span class="text-[10px] text-amber-400/80 block">Sent to Kafra Storage Vault</span>
                            </div>
                        </div>
                    </template>

                    <div class="border-t border-white/[0.08] pt-3 flex items-center justify-between">
                        <span class="text-xs text-[#788394] uppercase font-cinzel font-bold">Contribution Total:</span>
                        <div class="text-right">
                            <span class="font-cinzel text-2xl font-black text-white" x-text="'$' + current.price_formatted"></span>
                            <span class="text-[10px] text-[#788394] uppercase font-mono ml-1" x-text="current.currency"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Switcher Tabs -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-2 bg-[#101725] p-1 rounded-xl border border-white/[0.06] text-xs font-semibold">
                        <button type="button" @click="paymentMode = 'paypal'"
                            :class="paymentMode === 'paypal' ? 'bg-[#1B2432] text-white border border-[#C6A76A]/40 shadow-md font-cinzel' : 'text-[#788394] hover:text-white'"
                            class="py-2 px-3 rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <i data-lucide="credit-card" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                            <span>Instant PayPal</span>
                        </button>

                        <button type="button" @click="paymentMode = 'manual'"
                            :class="paymentMode === 'manual' ? 'bg-[#1B2432] text-white border border-[#C6A76A]/40 shadow-md font-cinzel' : 'text-[#788394] hover:text-white'"
                            class="py-2 px-3 rounded-lg transition-all flex items-center justify-center gap-1.5">
                            <i data-lucide="wallet" class="w-3.5 h-3.5 text-[#D8BE8A]"></i>
                            <span>Manual / Offline</span>
                        </button>
                    </div>

                    <!-- TAB 1: INSTANT PAYPAL CHECKOUT -->
                    <div x-show="paymentMode === 'paypal'" class="space-y-4">
                        @auth
                            @if(!empty($paypalConfig['enabled']) && !empty($paypalConfig['email']))
                                <form method="POST" :action="current.checkout_url" class="space-y-3">
                                    @csrf
                                    <button type="submit" class="prestige-btn-primary w-full py-3.5 text-xs font-bold flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-[#C6A76A]/20">
                                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                                        <span>Instant PayPal Checkout</span>
                                    </button>
                                </form>
                                <div class="flex items-center justify-center gap-1.5 text-[11px] text-[#788394]">
                                    <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-400"></i>
                                    <span>256-bit encrypted checkout via PayPal IPN</span>
                                </div>
                            @else
                                <div class="p-4 rounded-xl bg-[#101725] border border-white/[0.08] text-center text-xs text-[#788394] space-y-1">
                                    <i data-lucide="clock" class="w-5 h-5 text-[#C6A76A] mx-auto"></i>
                                    <p class="text-white font-cinzel">PayPal Gateway Under Configuration</p>
                                    <p class="text-[11px]">Please switch to the Manual / Offline tab to submit your transaction reference.</p>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="prestige-btn-primary w-full py-3 text-xs font-bold flex items-center justify-center gap-2">
                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                <span>Sign In to Acquire Points</span>
                            </a>
                        @endauth
                    </div>

                    <!-- TAB 2: MANUAL / OFFLINE CLAIM (GCASH, WIRE, CRYPTO) -->
                    <div x-show="paymentMode === 'manual'" class="space-y-4">
                        @if(!empty($donationSettings['payment_details']))
                            <div class="p-3.5 rounded-xl bg-[#101725] border border-white/[0.08] text-[11px] text-[#AAB3C1] space-y-2">
                                <div class="font-cinzel text-xs font-bold text-[#D8BE8A] flex items-center gap-1.5">
                                    <i data-lucide="wallet" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                                    <span>Accepted Payment Channels</span>
                                </div>
                                <div class="text-[#788394] whitespace-pre-line leading-relaxed font-sans max-h-36 overflow-y-auto pr-1">
                                    {{ $donationSettings['payment_details'] }}
                                </div>
                            </div>
                        @endif

                        @auth
                            <form method="POST" action="{{ route('donations.submit') }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="package_id" :value="current.id">

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#AAB3C1] uppercase tracking-wider mb-1 font-cinzel">Transaction Reference / Receipt ID</label>
                                    <input type="text" name="transaction_reference" required placeholder="e.g. GCash Ref, Bank Wire ID, or Crypto Hash"
                                        class="w-full px-3.5 py-2.5 bg-[#101725] border border-white/[0.12] focus:border-[#C6A76A] rounded-xl text-xs text-white placeholder-[#788394] outline-none">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#AAB3C1] uppercase tracking-wider mb-1 font-cinzel">Additional Notes</label>
                                    <textarea name="notes" rows="2" placeholder="Sender name, payment method, date/time..."
                                        class="w-full px-3.5 py-2 bg-[#101725] border border-white/[0.12] focus:border-[#C6A76A] rounded-xl text-xs text-white placeholder-[#788394] outline-none"></textarea>
                                </div>

                                <button type="submit" class="prestige-btn-primary w-full py-2.5 text-xs font-bold flex items-center justify-center gap-2 cursor-pointer">
                                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                    <span>Submit Claim for GM Verification</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="prestige-btn-primary w-full py-2.5 text-xs font-bold flex items-center justify-center gap-2">
                                <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                                <span>Sign In to Submit Claim</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Instant Fulfillment Guarantee Footer -->
                <div class="pt-4 border-t border-white/[0.06] text-[11px] text-[#788394] space-y-1">
                    <div class="flex items-center gap-1.5 text-[#D8BE8A] font-semibold font-cinzel">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                        <span>Guaranteed Instant Fulfillment</span>
                    </div>
                    <p class="leading-relaxed">Cash Shop points are automatically attached to your master account. Bonus items are safely delivered to your account's Kafra Storage Vault.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         PLAYER'S RECENT DONATION LEDGER (History)
         ====================================================================== -->
    @auth
        @if(count($userDonations) > 0)
            <div class="prestige-card overflow-hidden">
                <div class="p-5 border-b border-white/[0.08] flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="history" class="w-5 h-5 text-[#C6A76A]"></i>
                        <h3 class="font-cinzel text-base font-bold uppercase tracking-wider text-white">My Patronage Ledger</h3>
                    </div>
                    <span class="text-xs text-[#788394] font-mono">{{ count($userDonations) }} Records Logged</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#101725] text-[#788394] uppercase text-[10px] font-cinzel tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Tier Package</th>
                                <th class="py-3 px-4">Amount</th>
                                <th class="py-3 px-4">Points</th>
                                <th class="py-3 px-4">Method / Ref</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Vault Delivery</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04]">
                            @foreach($userDonations as $d)
                                <tr class="hover:bg-[#1B2432]/40 transition-colors">
                                    <td class="py-3.5 px-4 text-[#788394] font-mono">{{ $d->created_at->format('M d, Y') }}</td>
                                    <td class="py-3.5 px-4 font-bold text-white font-cinzel">{{ $d->package?->name ?? 'Custom Tier' }}</td>
                                    <td class="py-3.5 px-4 font-mono text-[#D8BE8A] font-bold">${{ number_format($d->amount, 2) }}</td>
                                    <td class="py-3.5 px-4 font-bold text-[#C6A76A] font-mono">{{ number_format($d->points_awarded) }}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="text-[11px] text-white uppercase font-mono">{{ $d->payment_method }}</div>
                                        <div class="text-[10px] text-[#788394] font-mono truncate max-w-[130px]">{{ $d->transaction_reference }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($d->status === 'completed')
                                            <span class="px-2.5 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Completed</span>
                                        @elseif($d->status === 'pending')
                                            <span class="px-2.5 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Pending</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded text-[9px] font-bold uppercase bg-red-500/20 text-red-300 border border-red-500/30">{{ ucfirst($d->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($d->delivered_at)
                                            <span class="text-emerald-400 font-semibold text-[11px] flex items-center justify-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                <span>Delivered</span>
                                            </span>
                                        @else
                                            <span class="text-[#788394] text-[11px]">Processing</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endauth

    <!-- ======================================================================
         INFORMATION FOUNDATION (Kafra Retrieval & Policy)
         ====================================================================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if(!empty($donationSettings['kafra_guide']))
            <div class="prestige-card p-6 space-y-3">
                <div class="flex items-center gap-2 text-[#C6A76A]">
                    <i data-lucide="package-check" class="w-5 h-5"></i>
                    <h3 class="font-cinzel text-sm font-bold uppercase tracking-wider text-white">Kafra Vault Retrieval</h3>
                </div>
                <div class="text-xs text-[#AAB3C1] whitespace-pre-line leading-relaxed font-sans">
                    {{ $donationSettings['kafra_guide'] }}
                </div>
            </div>
        @endif

        @if(!empty($donationSettings['terms']))
            <div class="prestige-card p-6 space-y-3">
                <div class="flex items-center gap-2 text-[#C6A76A]">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    <h3 class="font-cinzel text-sm font-bold uppercase tracking-wider text-white">Voluntary Support Terms</h3>
                </div>
                <div class="text-xs text-[#788394] whitespace-pre-line leading-relaxed font-sans">
                    {{ $donationSettings['terms'] }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
