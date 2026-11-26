@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <a href="{{ route('admin.redeem.index') }}" class="text-xs text-cyan-400 hover:underline">&larr; Back to Redeem Codes</a>
            <h1 class="text-2xl font-bold text-white mt-1">{{ $campaign->name }}</h1>
            <p class="text-xs text-slate-400">{{ \App\Models\RedeemCampaign::TYPES[$campaign->campaign_type] ?? $campaign->campaign_type }} • Status: <strong class="text-cyan-300 uppercase">{{ $campaign->status }}</strong></p>
        </div>
    </div>

    @if(session('generated_codes_plaintext'))
        <div class="p-5 rounded-2xl bg-emerald-950/60 border border-emerald-500/50 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-300">One-Time Generated Codes (Save Now — Plaintext Not Stored)</h3>
                <a href="{{ route('admin.redeem.codes.export', $campaign->id) }}" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-slate-950 text-xs font-bold">Export CSV</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-40 overflow-y-auto">
                @foreach(session('generated_codes_plaintext') as $plain)
                    <code class="px-2.5 py-1.5 rounded bg-slate-950 border border-emerald-500/30 text-xs text-emerald-200 font-mono select-all">{{ $plain }}</code>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Reward Configuration -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-cyan-400">Campaign Rewards</h2>
            <div class="space-y-2">
                @forelse($campaign->rewards as $rw)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs">
                        <div>
                            <span class="font-bold text-white">{{ $rw->formattedLabel() }}</span>
                            <span class="text-slate-500 ml-2 uppercase text-[10px]">{{ $rw->reward_type }} • {{ $rw->delivery_scope }}</span>
                        </div>
                        <form method="POST" action="{{ route('admin.redeem.rewards.delete', $rw->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-400 hover:underline text-xs cursor-pointer">Remove</button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">No rewards configured yet.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.redeem.rewards.store', $campaign->id) }}" class="pt-3 border-t border-slate-800 space-y-3 text-xs" x-data="{ rtype: 'cash_points' }">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Reward Type</label>
                        <select name="reward_type" x-model="rtype" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            <option value="cash_points">Cash Points</option>
                            <option value="zeny">Zeny</option>
                            <option value="item">Ragnarok Item</option>
                            <option value="custom">Custom Reward</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Amount / Quantity</label>
                        <input type="number" name="amount" value="100" min="1" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>

                <div x-show="rtype === 'item'" style="display: none;" class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Item ID (from item_db)</label>
                        <input type="number" name="item_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Refine (0–20)</label>
                        <input type="number" name="refine" value="0" min="0" max="20" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <label class="col-span-2 flex items-center gap-2 text-slate-300"><input type="checkbox" name="is_bound" value="1"> Account Bound</label>
                </div>
                <div x-show="rtype === 'custom'" style="display: none;">
                    <label class="block text-slate-400 mb-1">Custom Reward Label</label>
                    <input type="text" name="custom_label" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 text-slate-950 font-bold cursor-pointer">Add Reward</button>
            </form>
        </div>

        <!-- Code Generation -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400">Generate Redeem Codes</h2>
            <form method="POST" action="{{ route('admin.redeem.codes.generate', $campaign->id) }}" class="space-y-3 text-xs" x-data="{ mode: 'single_auto' }">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Generation Mode</label>
                        <select name="mode" x-model="mode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            <option value="single_auto">Single Auto-Generated</option>
                            <option value="single_manual">Single Manual Code</option>
                            <option value="bulk">Bulk Unique Codes</option>
                            <option value="shared">Shared Campaign Code</option>
                        </select>
                    </div>
                    <div x-show="mode === 'single_manual' || mode === 'shared'" style="display: none;">
                        <label class="block text-slate-400 mb-1">Custom Code</label>
                        <input type="text" name="manual_code" placeholder="WELCOME-2027" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white uppercase font-mono">
                    </div>
                    <div x-show="mode === 'single_auto' || mode === 'bulk'">
                        <label class="block text-slate-400 mb-1">Prefix (Optional)</label>
                        <input type="text" name="prefix" placeholder="LAUNCH" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white uppercase font-mono">
                    </div>
                    <div x-show="mode === 'bulk'" style="display: none;">
                        <label class="block text-slate-400 mb-1">Number of Codes (1–1000)</label>
                        <input type="number" name="count" value="10" min="1" max="1000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Max Redemptions per Code</label>
                        <input type="number" name="max_redemptions" value="1" min="1" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Bind Account ID (Optional)</label>
                        <input type="number" name="bound_account_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Expires At (Optional)</label>
                        <input type="datetime-local" name="expires_at" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold cursor-pointer">Generate Codes</button>
            </form>
        </div>
    </div>

    <!-- Codes Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-slate-800">
            <h2 class="text-xs font-bold uppercase tracking-wider text-white">Campaign Codes ({{ $campaign->codes->count() }})</h2>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                <tr>
                    <th class="py-2.5 px-4">Masked Hint</th>
                    <th class="py-2.5 px-4">Type</th>
                    <th class="py-2.5 px-4 text-center">Usage</th>
                    <th class="py-2.5 px-4">Status</th>
                    <th class="py-2.5 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($campaign->codes as $code)
                    <tr>
                        <td class="py-2.5 px-4 font-mono text-cyan-300">{{ $code->code_hint }}</td>
                        <td class="py-2.5 px-4 uppercase text-[10px]">{{ $code->code_type }}</td>
                        <td class="py-2.5 px-4 text-center font-mono">{{ $code->redemptions_count }} / {{ $code->max_redemptions }}</td>
                        <td class="py-2.5 px-4 uppercase font-bold text-[10px]">{{ $code->effectiveStatus() }}</td>
                        <td class="py-2.5 px-4 text-right">
                            <form method="POST" action="{{ route('admin.redeem.codes.toggle', $code->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] cursor-pointer">
                                    {{ $code->status === 'disabled' ? 'Enable' : 'Disable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-slate-500">No codes generated for this campaign yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
