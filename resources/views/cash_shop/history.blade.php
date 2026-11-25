@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Header Hero -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shrink-0">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight">Purchase History</h1>
                    <p class="text-xs text-slate-400 mt-1">Audit log of your web mall purchases and Escrow delivery statuses</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('cash-shop.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Back to Shop</span>
                </a>
                <a href="{{ route('escrow.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-md shadow-cyan-500/20 transition-all flex items-center gap-2">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                    <span>Escrow Inbox</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Purchases Table Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl shadow-2xl backdrop-blur-xl overflow-hidden">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between bg-slate-950/40">
            <h2 class="text-sm font-bold text-white tracking-wide">Orders Ledger</h2>
            <span class="text-xs font-mono px-3 py-1 rounded-full bg-slate-950 border border-slate-800 text-slate-300">
                <strong class="text-cyan-400">{{ $purchases->total() }}</strong> Total Order{{ $purchases->total() === 1 ? '' : 's' }}
            </span>
        </div>

        @if($purchases->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800/80 bg-slate-950/60 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-5">Date</th>
                            <th class="py-3 px-4">Order Code</th>
                            <th class="py-3 px-4">Item Details</th>
                            <th class="py-3 px-4">Cost</th>
                            <th class="py-3 px-4">Escrow Status</th>
                            <th class="py-3 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($purchases as $order)
                            <tr class="hover:bg-slate-950/40 transition-colors">
                                <td class="py-4 px-5 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                    {{ $order->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="py-4 px-4 font-mono font-bold text-cyan-400 whitespace-nowrap">
                                    {{ $order->purchase_code }}
                                </td>
                                <td class="py-4 px-4 min-w-[200px]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 p-1 flex items-center justify-center shrink-0">
                                            <img src="https://static.divine-pride.net/images/items/item/{{ $order->item_id }}.png" alt="item" class="w-7 h-7 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        </div>
                                        <div>
                                            <p class="font-bold text-white text-xs truncate">{{ $order->product_name }}</p>
                                            <p class="text-[10px] text-slate-400">
                                                ID: #{{ $order->item_id }} • {{ $order->total_quantity }}x Total
                                                @if($order->refine > 0)
                                                    • <span class="text-amber-400 font-bold">+{{ $order->refine }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-amber-300 text-xs">
                                        {{ number_format($order->total_cash_points) }} CP
                                    </span>
                                    <span class="block text-[10px] text-slate-500 font-mono">
                                        ({{ number_format($order->unit_price) }} CP ea)
                                    </span>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($order->escrowItem)
                                        @if($order->escrowItem->status === 'claimed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                <span>Transferred ({{ $order->escrowItem->claimed_destination_label }})</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-amber-500/15 text-amber-300 border border-amber-500/30 animate-pulse">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                <span>In Escrow Ready</span>
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-slate-400">
                                            Processed
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    @if($order->escrowItem && $order->escrowItem->status === 'unclaimed')
                                        <a href="{{ route('escrow.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors inline-flex items-center gap-1">
                                            <span>Transfer Item</span>
                                            <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-500 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($purchases->hasPages())
                <div class="p-5 border-t border-slate-800">
                    {{ $purchases->links() }}
                </div>
            @endif
        @else
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center mx-auto text-slate-600 mb-3">
                    <i data-lucide="receipt" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-white">No Purchase History</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                    You haven't purchased any items from the Cash Shop yet.
                </p>
                <div class="mt-4">
                    <a href="{{ route('cash-shop.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors">
                        Browse Cash Shop
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
