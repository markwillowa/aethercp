@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Server Donations & Cash Shop Points</h1>
        <p class="text-xs text-slate-400 mt-1">Support server hosting, anti-cheat licenses, and hardware upgrades.</p>
    </div>

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
                            <span>Total Received:</span>
                            <strong class="text-cyan-300 font-bold">{{ number_format($pkg->points + $pkg->bonus_points) }} Points</strong>
                        </div>
                    </div>
                </div>

                @auth
                    <button onclick="document.getElementById('manual_pkg_{{ $pkg->id }}').scrollIntoView({behavior: 'smooth'})"
                        class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Select Package</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-semibold text-xs transition-colors block">
                        Sign In to Donate
                    </a>
                @endauth
            </div>
        @endforeach
    </div>

    @auth
        <!-- Manual Donation Claim Form -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 sm:p-8 backdrop-blur-md shadow-xl max-w-2xl mx-auto space-y-4">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white">Submit Donation Proof / Reference</h3>
                <p class="text-xs text-slate-400 mt-0.5">Submit your transaction ID or reference number for manual admin crediting.</p>
            </div>

            <form method="POST" action="{{ route('donations.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Select Donation Tier</label>
                    <select name="package_id" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200">
                        @foreach($packages as $pkg)
                            <option id="manual_pkg_{{ $pkg->id }}" value="{{ $pkg->id }}">
                                {{ $pkg->name }} — ${{ number_format($pkg->price, 2) }} ({{ number_format($pkg->points + $pkg->bonus_points) }} Points)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Transaction Reference / Receipt ID</label>
                    <input type="text" name="transaction_reference" required placeholder="e.g. TXN-9482018 or PayPal ID"
                        class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200 placeholder-slate-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Additional Notes</label>
                    <textarea name="notes" rows="2" placeholder="Sender name or payment provider..."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 placeholder-slate-500"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-md transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Submit Donation for Verification</span>
                </button>
            </form>
        </div>

        @if(count($userDonations) > 0)
            <!-- User Donation History -->
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-4 border-b border-slate-800">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white">My Donation History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                            <tr>
                                <th class="py-2.5 px-4">Date</th>
                                <th class="py-2.5 px-4">Package</th>
                                <th class="py-2.5 px-4">Amount</th>
                                <th class="py-2.5 px-4">Points</th>
                                <th class="py-2.5 px-4">Reference</th>
                                <th class="py-2.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($userDonations as $d)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="py-3 px-4 text-slate-400">{{ $d->created_at->format('M d, Y') }}</td>
                                    <td class="py-3 px-4 font-bold text-white">{{ $d->package?->name ?? 'Custom' }}</td>
                                    <td class="py-3 px-4 font-mono">${{ number_format($d->amount, 2) }}</td>
                                    <td class="py-3 px-4 font-bold text-cyan-400">{{ number_format($d->points_awarded) }}</td>
                                    <td class="py-3 px-4 font-mono text-slate-400">{{ $d->transaction_reference }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $d->status === 'approved' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : ($d->status === 'pending' ? 'bg-amber-950 text-amber-400 border border-amber-500/30' : 'bg-red-950 text-red-400 border border-red-500/30') }}">
                                            {{ $d->status }}
                                        </span>
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
@endsection

