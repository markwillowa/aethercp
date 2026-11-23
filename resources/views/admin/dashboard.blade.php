@extends('layouts.admin')

@section('admin_content')
<div class="space-y-8">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Administration Dashboard</h1>
        <p class="text-xs text-slate-400 mt-1">Overview of game cluster, accounts, and server metrics.</p>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
                <span>Online Players</span>
                <i data-lucide="users" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ $onlinePlayers }}</p>
            <p class="text-[11px] text-emerald-400 mt-1 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                <span>Cluster active</span>
            </p>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
                <span>Registered Users</span>
                <i data-lucide="shield" class="w-4 h-4 text-cyan-400"></i>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ $totalWebUsers }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Web CP accounts</p>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
                <span>Pending Donations</span>
                <i data-lucide="credit-card" class="w-4 h-4 text-amber-400"></i>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ $pendingDonations }}</p>
            <p class="text-[11px] text-amber-400 mt-1">Requires review</p>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
                <span>News & Articles</span>
                <i data-lucide="newspaper" class="w-4 h-4 text-blue-400"></i>
            </div>
            <p class="text-3xl font-extrabold text-white">{{ $totalNews }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Published posts</p>
        </div>
    </div>

    <!-- 2 Col: Recent Logs & Recent Registrations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity Logs -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">Recent Audit Logs</h3>
                <a href="{{ route('admin.logs') }}" class="text-xs text-cyan-400 hover:underline">View All</a>
            </div>

            <div class="space-y-2 text-xs">
                @forelse($recentLogs as $log)
                    <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-1">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="font-semibold text-cyan-400">{{ $log->action }}</span>
                            <span class="text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-300">{{ $log->description }}</p>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 text-center py-4">No recent activity logs.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Registered Users -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">Recent Registrations</h3>
                <a href="{{ route('admin.accounts') }}" class="text-xs text-cyan-400 hover:underline">Manage All</a>
            </div>

            <div class="space-y-2 text-xs">
                @forelse($recentUsers as $u)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-white">{{ $u->username }}</span>
                            <span class="text-slate-500">(#{{ $u->account_id }})</span>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $u->created_at->format('M d, H:i') }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 text-center py-4">No recent registrations.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

