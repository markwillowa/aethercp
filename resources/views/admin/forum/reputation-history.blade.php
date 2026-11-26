@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Reputation Transaction History &amp; Adjustments</h1>
            <p class="text-xs text-slate-400 mt-1">Full immutable reputation ledger, moderator manual adjustments, and audit trail.</p>
        </div>
        <a href="{{ route('admin.forum.reputation.settings') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700">Back to Reputation Settings</a>
    </div>

    <!-- Manual Reputation Adjustment -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <h2 class="text-base font-bold text-white mb-4">Manual Reputation Adjustment (Audited)</h2>
        <form action="{{ route('admin.forum.reputation.adjust') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Member</label>
                <select name="recipient_user_id" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->username }} ({{ $u->role }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Points Delta (+/-)</label>
                <input type="number" name="points_delta" required placeholder="e.g. 15 or -10" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Mandatory Reason</label>
                <input type="text" name="reason" required minlength="3" maxlength="255" placeholder="Explain adjustment reason..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold">Apply Audited Adjustment</button>
            </div>
        </form>
    </div>

    <!-- Transaction Ledger -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-base font-bold text-white">Reputation Ledger</h2>
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Filter by username..." class="px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-200">
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-200">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-800">
                    <tr>
                        <th class="py-2 pr-4">Recipient</th>
                        <th class="py-2 pr-4">Source</th>
                        <th class="py-2 pr-4">Event</th>
                        <th class="py-2 pr-4">Description</th>
                        <th class="py-2 pr-4 text-right">Points</th>
                        <th class="py-2 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($transactions as $tx)
                        <tr>
                            <td class="py-3 pr-4 font-semibold text-white">{{ $tx->recipient?->username ?? 'Deleted' }}</td>
                            <td class="py-3 pr-4 text-slate-400">{{ $tx->source?->username ?? 'System' }}</td>
                            <td class="py-3 pr-4 text-cyan-300">{{ str_replace('_', ' ', ucfirst($tx->event_type)) }}</td>
                            <td class="py-3 pr-4 text-slate-300">{{ $tx->description }}</td>
                            <td class="py-3 pr-4 text-right font-mono font-bold {{ $tx->points >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $tx->points >= 0 ? '+' : '' }}{{ $tx->points }}
                            </td>
                            <td class="py-3 text-right text-slate-500">{{ $tx->created_at?->format('M j, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No reputation transactions recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $transactions->links() }}
    </div>

    <!-- Audit Logs -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 space-y-4">
        <h2 class="text-base font-bold text-white">Manual Adjustment Audit Log</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-800">
                    <tr>
                        <th class="py-2 pr-4">Admin</th>
                        <th class="py-2 pr-4">Recipient</th>
                        <th class="py-2 pr-4">Delta</th>
                        <th class="py-2 pr-4">Total Change</th>
                        <th class="py-2 pr-4">Reason</th>
                        <th class="py-2 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($auditLogs as $log)
                        <tr>
                            <td class="py-2.5 pr-4 text-amber-300">{{ $log->admin?->username }}</td>
                            <td class="py-2.5 pr-4 text-white">{{ $log->recipient?->username }}</td>
                            <td class="py-2.5 pr-4 font-mono font-bold {{ $log->points_delta >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $log->points_delta >= 0 ? '+' : '' }}{{ $log->points_delta }}
                            </td>
                            <td class="py-2.5 pr-4 font-mono text-slate-400">{{ $log->previous_total }} &rarr; {{ $log->new_total }}</td>
                            <td class="py-2.5 pr-4 text-slate-300">{{ $log->reason }}</td>
                            <td class="py-2.5 text-right text-slate-500">{{ $log->created_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-4 text-center text-slate-500">No manual adjustments recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
