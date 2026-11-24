@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6" x-data="{ triggerType: 'badge', rewardType: 'zeny' }">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Milestone Reward System</h1>
        <p class="text-xs text-slate-400 mt-1">Configure automatic Zeny &amp; Kafra Storage item rewards for ranking badges and tiers, with idempotent delivery tracking.</p>
    </div>

    <!-- Create Reward Form -->
    <form method="POST" action="{{ route('admin.rewards.store') }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-5">
        @csrf
        <h2 class="text-sm font-bold uppercase tracking-wider text-cyan-400 border-b border-slate-800 pb-3">Create Milestone Reward</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Reward Name</label>
                <input type="text" name="name" required placeholder="e.g. Bronze PvP Bonus" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Category</label>
                <select name="category" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    <option value="level">Level</option>
                    <option value="job">Job</option>
                    <option value="pvp">PvP</option>
                    <option value="mvp">MVP</option>
                    <option value="guild">Guild</option>
                    <option value="badge">Badge</option>
                </select>
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Trigger Type</label>
                <select name="trigger_type" x-model="triggerType" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    <option value="badge">Badge Definition</option>
                    <option value="tier">PvP / MVP Star Tier</option>
                </select>
            </div>
            <div x-show="triggerType === 'badge'">
                <label class="block text-slate-400 font-semibold mb-1.5">Qualifying Badge</label>
                <select name="badge_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    @foreach($badges as $b)
                        <option value="{{ $b->id }}">{{ $b->name }} ({{ ucfirst($b->category) }})</option>
                    @endforeach
                </select>
            </div>
            <div x-show="triggerType === 'tier'" style="display: none;">
                <label class="block text-slate-400 font-semibold mb-1.5">Qualifying Tier</label>
                <select name="badge_tier_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    @foreach($tiers as $t)
                        <option value="{{ $t->id }}">[{{ strtoupper($t->category) }}] {{ $t->formatted_label }} ({{ $t->min_kills }} kills)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Reward Type</label>
                <select name="reward_type" x-model="rewardType" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    <option value="zeny">Zeny (Character Balance)</option>
                    <option value="item">Single Item (Kafra Storage)</option>
                    <option value="multi_item">Zeny + Multiple Items</option>
                </select>
            </div>
            <div x-show="rewardType === 'zeny' || rewardType === 'multi_item'">
                <label class="block text-slate-400 font-semibold mb-1.5">Zeny Amount</label>
                <input type="number" name="zeny_amount" value="10000" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
            </div>
            <div x-show="rewardType === 'item'" style="display: none;">
                <label class="block text-slate-400 font-semibold mb-1.5">Item ID (rAthena)</label>
                <input type="number" name="item_id" placeholder="e.g. 607" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
            </div>
            <div x-show="rewardType === 'item'" style="display: none;">
                <label class="block text-slate-400 font-semibold mb-1.5">Item Quantity</label>
                <input type="number" name="item_amount" value="1" min="1" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
            </div>
            <div x-show="rewardType === 'multi_item'" style="display: none;" class="sm:col-span-2">
                <label class="block text-slate-400 font-semibold mb-1.5">Items (format: itemId:qty, itemId:qty)</label>
                <input type="text" name="items_raw" placeholder="607:5, 984:10" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Repeatability Policy</label>
                <select name="policy" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    <option value="one_time">One-Time Lifetime</option>
                    <option value="per_season">Once Per Season</option>
                    <option value="per_period">Once Per Period</option>
                    <option value="repeatable">Repeatable</option>
                </select>
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Season / Period Key (Optional)</label>
                <input type="text" name="period_key" placeholder="e.g. S1 or 2026-11" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 text-xs pt-2">
            <input type="text" name="message" placeholder="Congratulations message shown to player..." class="flex-1 min-w-[240px] bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
                <input type="checkbox" name="is_enabled" value="1" checked class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                <span>Enabled</span>
            </label>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold">Create Reward</button>
        </div>
    </form>

    <!-- Configured Rewards Table -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-800 bg-slate-950/60">
            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Configured Milestone Rewards</h3>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                <tr>
                    <th class="py-3 px-4">Reward</th>
                    <th class="py-3 px-4">Qualifying Badge / Tier</th>
                    <th class="py-3 px-4">Payload</th>
                    <th class="py-3 px-4">Policy</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($rewards as $rw)
                    <tr>
                        <td class="py-3 px-4 font-bold text-white">{{ $rw->name }}</td>
                        <td class="py-3 px-4 text-cyan-400 font-semibold">
                            {{ $rw->badge ? $rw->badge->name : ($rw->tier ? '[' . strtoupper($rw->tier->category) . '] ' . $rw->tier->formatted_label : '-') }}
                        </td>
                        <td class="py-3 px-4 font-mono text-amber-300">
                            @if($rw->zeny_amount > 0) {{ number_format($rw->zeny_amount) }} Z @endif
                            @if(!empty($rw->items_json))
                                @foreach($rw->items_json as $it)
                                    <span class="ml-1 text-slate-300">[#{{ $it['nameid'] }} x{{ $it['amount'] }}]</span>
                                @endforeach
                            @endif
                        </td>
                        <td class="py-3 px-4 uppercase font-mono text-slate-400">{{ $rw->policy }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $rw->is_enabled ? 'bg-emerald-950 text-emerald-400' : 'bg-slate-800 text-slate-500' }}">
                                {{ $rw->is_enabled ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <form method="POST" action="{{ route('admin.rewards.toggle', $rw->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-lg text-[11px] font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200">
                                    {{ $rw->is_enabled ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-slate-500">No milestone rewards configured yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Delivery History & Failure Recovery -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-800 bg-slate-950/60">
            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Reward Delivery History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">TX Ref</th>
                        <th class="py-3 px-4">Reward</th>
                        <th class="py-3 px-4">Recipient</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Attempts / Error</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($issuances as $iss)
                        <tr>
                            <td class="py-2.5 px-4 font-mono text-cyan-400">{{ $iss->transaction_ref }}</td>
                            <td class="py-2.5 px-4 text-white font-semibold">{{ $iss->definition?->name ?? 'Reward' }}</td>
                            <td class="py-2.5 px-4 font-mono text-slate-300">Char #{{ $iss->char_id ?? '-' }} (Acc #{{ $iss->account_id ?? '-' }})</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $iss->status === 'delivered' ? 'bg-emerald-950 text-emerald-400' : ($iss->status === 'retry_required' ? 'bg-amber-950 text-amber-400' : 'bg-red-950 text-red-400') }}">
                                    {{ $iss->status }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-slate-400">
                                <span class="font-mono">#{{ $iss->attempts }}</span>
                                @if($iss->last_error)
                                    <span class="text-red-400 block text-[10px]">{{ $iss->last_error }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right">
                                @if(in_array($iss->status, ['failed', 'retry_required', 'pending'], true))
                                    <form method="POST" action="{{ route('admin.rewards.retry', $iss->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-[11px] font-semibold">Retry</button>
                                    </form>
                                @else
                                    <span class="text-slate-500 text-[11px]">{{ $iss->delivered_at?->diffForHumans() }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No reward deliveries recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
