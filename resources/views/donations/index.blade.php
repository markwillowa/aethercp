@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Server Donations & Cash Shop Points</h1>
        <p class="text-xs text-slate-400 mt-1">Support server hosting, anti-cheat licenses, and hardware upgrades with instant automatic delivery.</p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Package Tiers -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($packages as $pkg)
            <div class="relative bg-slate-900/80 border {{ $pkg->is_popular ? 'border-cyan-500 shadow-2xl aether-glow-cyan' : 'border-slate-800' }} rounded-2xl p-6 backdrop-blur-md flex flex-col justify-between space-y-6">
                @if($pkg->is_popular)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-cyan-500 text-slate-950 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow-md">
                        Most Popular
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $pkg->name }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ $pkg->description }}</p>
                    </div>

                    <div class="border-y border-slate-800 py-4 text-center">
                        <span class="text-3xl font-extrabold text-white">${{ number_format($pkg->price, 2) }}</span>
                        <span class="text-xs text-slate-500 uppercase ml-1">{{ $pkg->currency }}</span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-300">
                            <span>Cash Points:</span>
                            <strong class="text-cyan-400 font-bold text-sm">{{ number_format($pkg->points) }}</strong>
                        </div>
                        @if($pkg->bonus_points > 0)
                            <div class="flex items-center justify-between text-emerald-400">
                                <span>Bonus Points:</span>
                                <strong>+{{ number_format($pkg->bonus_points) }}</strong>
                            </div>
                        @endif
                        <div class="flex items-center justify-between border-t border-slate-800/80 pt-2 text-white font-semibold">
                            <span>Total Points:</span>
                            <strong class="text-cyan-300 font-bold">{{ number_format($pkg->points + $pkg->bonus_points) }} Points</strong>
                        </div>

                        @if($pkg->item_amount > 0 && ($pkg->item_name || $pkg->item_id))
                            <div class="mt-3 p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 flex items-start gap-2">
                                <i data-lucide="gift" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i>
                                <div class="text-[11px]">
                                    <div class="font-bold text-amber-200">+{{ $pkg->item_amount }}x {{ $pkg->item_name ?: 'Bonus Item #' . $pkg->item_id }}</div>
                                    <div class="text-[10px] text-amber-400/80">Delivered directly to Kafra Storage</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    @auth
                        @if(!empty($paypalConfig['enabled']) && !empty($paypalConfig['email']))
                            <form method="POST" action="{{ route('donations.paypal.checkout', $pkg->id) }}" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                                    <span>Instant PayPal Checkout</span>
                                </button>
                            </form>
                            <button type="button" onclick="selectAndScrollPackage({{ $pkg->id }})"
                                class="w-full py-1.5 px-3 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-[11px] transition-colors flex items-center justify-center gap-1 cursor-pointer">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Manual Reference Claim</span>
                            </button>
                        @else
                            <button type="button" onclick="selectAndScrollPackage({{ $pkg->id }})"
                                class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Select Tier for Manual Claim</span>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-semibold text-xs transition-colors block">
                            Sign In to Donate
                        </a>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    @auth
        <!-- Manual Donation Claim Form -->
        <div id="manual-claim-card" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 sm:p-8 backdrop-blur-md shadow-xl max-w-2xl mx-auto space-y-4">
            <div class="border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="receipt" class="w-5 h-5 text-cyan-400"></i>
                    <h3 class="text-base font-bold text-white">Manual Donation Claim</h3>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">If you donated via Bank Wire, G-Cash, Crypto, or manual PayPal transfer, submit your transaction ID for staff verification.</p>
            </div>

            <form method="POST" action="{{ route('donations.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Select Donation Tier</label>
                    <select id="manual_package_select" name="package_id" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200">
                        @foreach($packages as $pkg)
                            <option id="manual_pkg_{{ $pkg->id }}" value="{{ $pkg->id }}">
                                {{ $pkg->name }} — ${{ number_format($pkg->price, 2) }} ({{ number_format($pkg->points + $pkg->bonus_points) }} Points{{ $pkg->item_amount > 0 ? ' + ' . $pkg->item_amount . 'x ' . ($pkg->item_name ?: 'Bonus Item') : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Transaction Reference / Receipt ID</label>
                    <input type="text" name="transaction_reference" required placeholder="e.g. TXN-9482018 or PayPal Transaction ID"
                        class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200 placeholder-slate-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Additional Notes</label>
                    <textarea name="notes" rows="2" placeholder="Sender name, payment provider, or date of transfer..."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 placeholder-slate-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-md transition-colors flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Submit Donation for Verification</span>
                </button>
            </form>
        </div>

        @if(count($userDonations) > 0)
            <!-- User Donation History -->
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-cyan-400"></i>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white">My Donation History</h3>
                    </div>
                    <span class="text-xs text-slate-400">{{ count($userDonations) }} records</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                            <tr>
                                <th class="py-2.5 px-4">Date</th>
                                <th class="py-2.5 px-4">Package</th>
                                <th class="py-2.5 px-4">Amount</th>
                                <th class="py-2.5 px-4">Points</th>
                                <th class="py-2.5 px-4">Bonus Items</th>
                                <th class="py-2.5 px-4">Method / Ref</th>
                                <th class="py-2.5 px-4 text-center">Status</th>
                                <th class="py-2.5 px-4 text-center">In-Game Delivery</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($userDonations as $d)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="py-3 px-4 text-slate-400">{{ $d->created_at->format('M d, Y') }}</td>
                                    <td class="py-3 px-4 font-bold text-white">{{ $d->package?->name ?? 'Custom' }}</td>
                                    <td class="py-3 px-4 font-mono">${{ number_format($d->amount, 2) }}</td>
                                    <td class="py-3 px-4 font-bold text-cyan-400">{{ number_format($d->points_awarded) }}</td>
                                    <td class="py-3 px-4">
                                        @if($d->package && $d->package->item_amount > 0)
                                            <span class="text-amber-300 font-medium">{{ $d->package->item_amount }}x {{ $d->package->item_name ?: 'Item #'.$d->package->item_id }}</span>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-mono text-slate-300 text-[11px]">{{ $d->transaction_reference }}</div>
                                        <div class="text-[10px] text-slate-500 uppercase">{{ $d->payment_method }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $d->status === 'approved' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : ($d->status === 'pending' ? 'bg-amber-950 text-amber-400 border border-amber-500/30' : 'bg-red-950 text-red-400 border border-red-500/30') }}">
                                            {{ $d->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($d->status === 'approved')
                                            <div class="flex flex-col items-center gap-1 text-[10px]">
                                                @if($d->points_delivered)
                                                    <span class="text-emerald-400 flex items-center gap-1">
                                                        <i data-lucide="check" class="w-3 h-3"></i> Points Credited
                                                    </span>
                                                @else
                                                    <span class="text-amber-400 flex items-center gap-1">
                                                        <i data-lucide="clock" class="w-3 h-3"></i> Points Pending
                                                    </span>
                                                @endif

                                                @if($d->package && $d->package->item_amount > 0)
                                                    @if($d->items_delivered)
                                                        <span class="text-emerald-400 flex items-center gap-1">
                                                            <i data-lucide="package-check" class="w-3 h-3"></i> Storage Item Delivered
                                                        </span>
                                                    @else
                                                        <span class="text-amber-400 flex items-center gap-1">
                                                            <i data-lucide="clock" class="w-3 h-3"></i> Storage Item Pending
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-500 text-[11px]">Awaiting Approval</span>
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
</div>

<script>
    function selectAndScrollPackage(pkgId) {
        var opt = document.getElementById('manual_pkg_' + pkgId);
        if (opt) {
            opt.selected = true;
        }
        var target = document.getElementById('manual-claim-card');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
@endsection
