@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">System Activity & Audit Logs</h1>
            <p class="text-xs text-slate-400 mt-1">Security logs, logins, administrative actions, and unstuck history.</p>
        </div>
        <button type="button" @click="$dispatch('open-dev-donation-modal')" class="px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 via-pink-500 to-rose-500 hover:from-amber-400 hover:to-pink-400 text-slate-950 shadow-lg shadow-pink-500/20 transition-all flex items-center gap-2 shrink-0">
            <i data-lucide="heart" class="w-4 h-4 text-slate-950 fill-current"></i>
            <span>Support Developer</span>
        </button>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                <tr>
                    <th class="py-3 px-4">Timestamp</th>
                    <th class="py-3 px-4">Action</th>
                    <th class="py-3 px-4">Account ID</th>
                    <th class="py-3 px-4">Description</th>
                    <th class="py-3 px-4">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-800/30">
                        <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-3 px-4 font-bold text-cyan-400">{{ $log->action }}</td>
                        <td class="py-3 px-4 font-mono text-slate-300">#{{ $log->account_id ?? '-' }}</td>
                        <td class="py-3 px-4 text-slate-200">{{ $log->description }}</td>
                        <td class="py-3 px-4 text-slate-500 font-mono">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">No activity logs recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-2">
        {{ $logs->links() }}
    </div>
</div>
@endsection

