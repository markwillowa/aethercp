@extends('layouts.admin')

@section('title', 'Cash Shop Purchase Ledger')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.cash-shop.index') }}" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white">Cash Shop Purchase Ledger</h1>
                <p class="text-xs text-slate-400 mt-0.5">Comprehensive audit trail of all in-game item mall transactions and Escrow Inbox handoffs.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 font-mono text-xs">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300">
                Total Sales: <strong class="text-cyan-400">{{ number_format($stats['total_sales']) }}</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300">
                Volume: <strong class="text-amber-400">{{ number_format($stats['total_cp']) }} CP</strong>
            </span>
        </div>
    </div>

    <!-- Search & Status Filter -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
        <form action="{{ route('admin.cash-shop.purchases') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by purchase code, account ID, username, or item..."
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-amber-500">
            </div>

            <div class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:border-amber-500 cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white cursor-pointer">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Ref Code</th>
                        <th class="py-3.5 px-4">Player / Account</th>
                        <th class="py-3.5 px-4">Product Purchased</th>
                        <th class="py-3.5 px-4 text-right">CP Spent</th>
                        <th class="py-3.5 px-4 text-center">Escrow Delivery</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-amber-300">
                                {{ $p->purchase_code }}
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white">{{ $p->user->username ?? 'Unknown' }}</span>
                                <span class="text-[10px] text-slate-400 block font-mono">Account #{{ $p->account_id }}</span>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white block">{{ $p->product_name_snapshot }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">Item #{{ $p->item_id }} • {{ $p->item_quantity }}x</span>
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-bold text-amber-300">
                                {{ number_format($p->cash_points_spent) }} CP
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($p->escrowItem)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $p->escrowItem->status === 'claimed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' }}">
                                        {{ $p->escrowItem->status === 'claimed' ? 'Delivered In-Game' : 'Ready In Escrow' }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-500 font-mono">—</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $p->status === 'completed' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                {{ $p->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="receipt" class="w-10 h-10 text-slate-600 mx-auto mb-2"></i>
                                <p class="text-sm font-bold text-white">No purchases recorded yet</p>
                                <p class="text-xs text-slate-500 mt-1">Player purchases made in the Cash Shop will appear here with full audit trails.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
