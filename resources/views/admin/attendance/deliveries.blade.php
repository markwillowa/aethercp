@extends('layouts.admin')

@section('title', 'Daily Reward Delivery Logs - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.attendance.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-medium">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Campaigns</span>
                </a>
                <span class="text-xs text-slate-600">/</span>
                <span class="text-xs text-cyan-400 font-medium">Fulfillment Logs</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">
                Reward Delivery Logs &amp; Retry Console
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Audit trail of every single daily reward delivery across AetherCP Web Inbox, Kafra Storage, and Character balances.
            </p>
        </div>
    </div>

    <!-- Filter Console -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.attendance.deliveries') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 focus:border-cyan-500 focus:outline-none cursor-pointer">
                <option value="">All Delivery Statuses</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered Successfully</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed / Error</option>
            </select>

            <select name="reward_type" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 focus:border-cyan-500 focus:outline-none cursor-pointer">
                <option value="">All Reward Types</option>
                <option value="item" {{ request('reward_type') === 'item' ? 'selected' : '' }}>Items</option>
                <option value="zeny" {{ request('reward_type') === 'zeny' ? 'selected' : '' }}>Zeny</option>
                <option value="cashpoints" {{ request('reward_type') === 'cashpoints' ? 'selected' : '' }}>Cash Points</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition-colors">Filter</button>
            @if(request('status') || request('reward_type'))
                <a href="{{ route('admin.attendance.deliveries') }}" class="px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-white transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 border-b border-slate-800 text-[10px] uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="py-3.5 px-4">Delivery Ref</th>
                        <th class="py-3.5 px-4">Campaign &amp; Day</th>
                        <th class="py-3.5 px-4">Recipient Account</th>
                        <th class="py-3.5 px-4">Reward Content</th>
                        <th class="py-3.5 px-4">Destination Target</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Timestamp</th>
                        <th class="py-3.5 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($deliveries as $del)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-400">
                                #{{ $del->id }}
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white block">{{ $del->claim?->campaign?->name ?? 'Unknown Campaign' }}</span>
                                <span class="text-[10px] text-cyan-400 font-mono">Day {{ $del->claim?->day_number }}</span>
                            </td>

                            <td class="py-3.5 px-4 font-mono">
                                <span class="text-slate-200">Account #{{ $del->claim?->account_id }}</span>
                                @if($del->claim?->character_id)
                                    <span class="text-[10px] text-slate-500 block">Char #{{ $del->claim?->character_id }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if($del->reward_type === 'zeny')
                                    <span class="font-bold text-amber-300 font-mono">{{ number_format($del->item?->zeny_amount ?? 0) }} Zeny</span>
                                @elseif($del->reward_type === 'cashpoints')
                                    <span class="font-bold text-purple-300 font-mono">{{ number_format($del->item?->cashpoints_amount ?? 0) }} CP</span>
                                @else
                                    <div class="flex items-center gap-1.5">
                                        @if(!empty($del->item?->item_id))
                                            <img src="https://static.divine-pride.net/images/items/item/{{ $del->item?->item_id }}.png" alt="" class="w-4 h-4 object-contain" onerror="this.style.display='none';">
                                        @endif
                                        <span class="font-bold text-slate-200">{{ $del->item?->formatted_title ?? "Item #{$del->item?->item_id}" }}</span>
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-300">
                                {{ $del->destination_target ?: 'Pending Assignment' }}
                                @if($del->failure_reason)
                                    <span class="text-rose-400 text-[10px] block mt-0.5">Error: {{ $del->failure_reason }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $del->status === 'delivered' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : ($del->status === 'failed' ? 'bg-rose-950 text-rose-400 border border-rose-500/30' : 'bg-amber-950 text-amber-400 border border-amber-500/30') }}">
                                    {{ $del->status }} ({{ $del->attempts }}x)
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                {{ $del->delivered_at ? $del->delivered_at->format('M d, H:i:s') : $del->created_at->format('M d, H:i:s') }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                @if($del->status === 'failed' || $del->status === 'pending')
                                    <form method="POST" action="{{ route('admin.attendance.deliveries.retry', $del->id) }}">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-cyan-950 hover:bg-cyan-900 text-cyan-300 border border-cyan-500/30 transition-colors" title="Retry Delivery">
                                            Retry
                                        </button>
                                    </form>
                                @else
                                    <span class="text-emerald-500 font-mono text-xs">✓ Done</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                                <p class="text-xs font-semibold text-slate-300">No reward deliveries recorded yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deliveries->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $deliveries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
