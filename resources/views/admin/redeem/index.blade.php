@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6" x-data="{ tab: 'dashboard', createOpen: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i data-lucide="ticket" class="w-6 h-6 text-emerald-400"></i>
                <span>Redeem Code Management</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage promotional campaigns, hashed redeem codes, multi-reward delivery, and audit logs.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.redeem.logs') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 inline-flex items-center gap-1.5">
                <i data-lucide="history" class="w-3.5 h-3.5 text-cyan-400"></i>
                <span>Redemption Logs</span>
            </a>
            <button type="button" @click="createOpen = true" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 inline-flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Campaign</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap gap-2">
        <button type="button" @click="tab = 'dashboard'" :class="tab === 'dashboard' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50' : 'bg-slate-900 text-slate-400 border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-semibold border cursor-pointer">Dashboard &amp; Metrics</button>
        <button type="button" @click="tab = 'campaigns'" :class="tab === 'campaigns' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50' : 'bg-slate-900 text-slate-400 border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-semibold border cursor-pointer">Campaigns ({{ $campaigns->count() }})</button>
        <button type="button" @click="tab = 'permissions'" :class="tab === 'permissions' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50' : 'bg-slate-900 text-slate-400 border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-semibold border cursor-pointer">Staff Permissions</button>
    </div>

    <!-- TAB 1: Dashboard -->
    <div x-show="tab === 'dashboard'" class="space-y-6">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.redeem.index') }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4 flex flex-wrap items-end gap-3 text-xs">
            <div>
                <label class="block text-slate-400 mb-1">Campaign</label>
                <select name="campaign_id" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    <option value="">All Campaigns</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}" {{ $campaignId === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-400 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 text-slate-950 font-bold cursor-pointer">Apply Filter</button>
            <a href="{{ route('admin.redeem.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 text-slate-300">Reset</a>
        </form>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 text-xs">
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400">Total Campaigns</span>
                <p class="text-2xl font-black text-white mt-1">{{ $stats['total_campaigns'] }}</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400">Generated Codes</span>
                <p class="text-2xl font-black text-cyan-400 mt-1">{{ $stats['total_codes'] }}</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400">Available Codes</span>
                <p class="text-2xl font-black text-emerald-400 mt-1">{{ $stats['available_codes'] }}</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400">Redeemed</span>
                <p class="text-2xl font-black text-amber-300 mt-1">{{ $stats['redeemed_codes'] }}</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400">Expired / Disabled</span>
                <p class="text-2xl font-black text-rose-400 mt-1">{{ $stats['expired_codes'] }} / {{ $stats['disabled_codes'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
                <span class="text-slate-400">Total Cash Points Distributed</span>
                <p class="text-2xl font-black text-cyan-400 mt-1 font-mono">{{ number_format($stats['total_cash_points']) }} CP</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
                <span class="text-slate-400">Total Zeny Distributed</span>
                <p class="text-2xl font-black text-amber-400 mt-1 font-mono">{{ number_format($stats['total_zeny']) }} Z</p>
            </div>
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
                <span class="text-slate-400">Total Items Distributed</span>
                <p class="text-2xl font-black text-emerald-400 mt-1 font-mono">{{ number_format($stats['total_items']) }}</p>
            </div>
        </div>

        <!-- Recent Redemption Activity -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">Recent Redemption Activity</h2>
                <a href="{{ route('admin.redeem.logs') }}" class="text-xs text-cyan-400 hover:underline">View Full Logs</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-4">Code</th>
                            <th class="py-2.5 px-4">Campaign</th>
                            <th class="py-2.5 px-4">Account / Char</th>
                            <th class="py-2.5 px-4">Status</th>
                            <th class="py-2.5 px-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($stats['recent_redemptions'] as $r)
                            <tr class="hover:bg-slate-800/30">
                                <td class="py-2.5 px-4 font-mono text-cyan-300">{{ $r->code_hint }}</td>
                                <td class="py-2.5 px-4 text-white">{{ $r->campaign?->name ?? '—' }}</td>
                                <td class="py-2.5 px-4 text-slate-300">{{ $r->username_snapshot }} (#{{ $r->account_id }})</td>
                                <td class="py-2.5 px-4 uppercase font-bold text-[10px]">{{ $r->status }}</td>
                                <td class="py-2.5 px-4 text-slate-400">{{ $r->redeemed_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-slate-500">No redemptions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Campaigns -->
    <div x-show="tab === 'campaigns'" style="display: none;" class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                <tr>
                    <th class="py-3 px-4">Campaign</th>
                    <th class="py-3 px-4">Type</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-center">Codes</th>
                    <th class="py-3 px-4 text-center">Redemptions</th>
                    <th class="py-3 px-4 text-right">Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($campaigns as $c)
                    <tr class="hover:bg-slate-800/30">
                        <td class="py-3 px-4">
                            <span class="font-bold text-white block">{{ $c->name }}</span>
                            <span class="text-[11px] text-slate-400">{{ \Illuminate\Support\Str::limit($c->description, 60) }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-300">{{ \App\Models\RedeemCampaign::TYPES[$c->campaign_type] ?? $c->campaign_type }}</td>
                        <td class="py-3 px-4 uppercase font-bold text-[10px] text-cyan-300">{{ $c->status }}</td>
                        <td class="py-3 px-4 text-center font-mono text-white">{{ $c->codes_count }}</td>
                        <td class="py-3 px-4 text-center font-mono text-emerald-400">{{ $c->redemptions_count }}</td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('admin.redeem.show', $c->id) }}" class="px-3 py-1.5 rounded-lg bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/30 font-semibold">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-slate-500">No campaigns created yet. Click "New Campaign" to begin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TAB 3: Staff Permissions -->
    <div x-show="tab === 'permissions'" style="display: none;" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.redeem.permissions.update') }}" class="space-y-4">
            @csrf
            <div>
                <h2 class="text-sm font-bold text-white">Granular Staff Permissions</h2>
                <p class="text-xs text-slate-400 mt-0.5">Administrators automatically hold all permissions. Configure which Redeem Code actions <code class="text-cyan-400">staff</code> accounts can perform.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                @foreach($permissions as $pKey => $pLabel)
                    <label class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-950/60 border border-slate-800 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $pKey }}" {{ in_array($pKey, $staffPerms, true) ? 'checked' : '' }} class="rounded border-slate-700 text-cyan-500">
                        <span class="text-slate-200 font-medium">{{ $pLabel }}</span>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold cursor-pointer">Save Permissions</button>
        </form>
    </div>

    <!-- Create Campaign Modal -->
    <div x-show="createOpen" style="display: none;" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div @click.away="createOpen = false" class="w-full max-w-2xl bg-slate-900 border border-slate-700 rounded-2xl p-6 space-y-4 my-8">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-cyan-400">Create Redemption Campaign</h3>
                <button type="button" @click="createOpen = false" class="text-slate-400 hover:text-white cursor-pointer"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.redeem.campaigns.store') }}" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-400 mb-1">Campaign Name</label>
                        <input type="text" name="name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Campaign Type</label>
                        <select name="campaign_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            @foreach(\App\Models\RedeemCampaign::TYPES as $tKey => $tLabel)
                                <option value="{{ $tKey }}">{{ $tLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Status</label>
                        <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            @foreach(\App\Models\RedeemCampaign::STATUSES as $st)
                                <option value="{{ $st }}" {{ $st === 'active' ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Max Per Account</label>
                        <input type="number" name="max_per_account" value="1" min="1" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Starts At (Optional)</label>
                        <input type="datetime-local" name="starts_at" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Ends At (Optional)</label>
                        <input type="datetime-local" name="ends_at" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white"></textarea>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Redemption Instructions</label>
                    <textarea name="instructions" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white"></textarea>
                </div>
                <div class="flex flex-wrap gap-4 pt-1">
                    <label class="flex items-center gap-2 text-slate-200"><input type="checkbox" name="one_per_account" value="1" checked> One per account</label>
                    <label class="flex items-center gap-2 text-slate-200"><input type="checkbox" name="one_per_character" value="1"> One per character</label>
                    <label class="flex items-center gap-2 text-slate-200"><input type="checkbox" name="is_public" value="1"> Publicly visible</label>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                    <button type="button" @click="createOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-500 text-slate-950 font-bold cursor-pointer">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
