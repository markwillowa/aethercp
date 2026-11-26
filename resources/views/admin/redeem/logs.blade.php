@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <a href="{{ route('admin.redeem.index') }}" class="text-xs text-cyan-400 hover:underline">&larr; Back to Redeem Dashboard</a>
            <h1 class="text-2xl font-bold text-white mt-1">Redemption History &amp; Delivery Logs</h1>
        </div>
        <a href="{{ route('admin.redeem.logs.export') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-slate-700">Export CSV</a>
    </div>

    <form method="GET" action="{{ route('admin.redeem.logs') }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4 flex flex-wrap items-end gap-3 text-xs">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code hint, username, account..." class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white w-64">
        <select name="campaign_id" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            <option value="">All Campaigns</option>
            @foreach($campaigns as $c)
                <option value="{{ $c->id }}" {{ (int) request('campaign_id') === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="status" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            <option value="">All Statuses</option>
            @foreach(\App\Models\RedeemRedemption::STATUSES as $st)
                <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 text-slate-950 font-bold cursor-pointer">Filter</button>
    </form>

    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                <tr>
                    <th class="py-3 px-4">Code &amp; Campaign</th>
                    <th class="py-3 px-4">Account / Character</th>
                    <th class="py-3 px-4">Rewards &amp; Deliveries</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($redemptions as $r)
                    <tr>
                        <td class="py-3 px-4">
                            <span class="font-mono text-cyan-300 block">{{ $r->code_hint }}</span>
                            <span class="text-slate-400">{{ $r->campaign?->name }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-white font-semibold block">{{ $r->username_snapshot }} (#{{ $r->account_id }})</span>
                            @if($r->character_name_snapshot)
                                <span class="text-[11px] text-slate-400">{{ $r->character_name_snapshot }} (#{{ $r->character_id }})</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 space-y-1">
                            @foreach($r->deliveries as $del)
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $del->status === 'delivered' ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400' }}">
                                        {{ $del->status }}
                                    </span>
                                    <span class="text-slate-300">{{ $del->reward_type }} ({{ $del->item_name ?: number_format($del->amount) }})</span>
                                    @if($del->status === 'failed')
                                        <form method="POST" action="{{ route('admin.redeem.deliveries.retry', $del->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold cursor-pointer">Retry</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 uppercase font-bold text-[10px]">{{ $r->status }}</td>
                        <td class="py-3 px-4 text-slate-400">{{ $r->redeemed_at?->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-slate-500">No redemption records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $redemptions->links() }}</div>
</div>
@endsection
