@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <!-- Header / Overview Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 sm:p-8 backdrop-blur-md shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-2xl font-black text-slate-950 shadow-lg shadow-cyan-500/20">
                {{ strtoupper(substr($user->username, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold text-white">{{ $user->username }}</h1>
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                        {{ $user->role }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-950 text-emerald-400 border border-emerald-500/30">
                        Account ID: {{ $user->account_id }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                    <span>Registered: {{ $user->created_at->format('M d, Y') }}</span>
                    <span>•</span>
                    <span>Last Login: {{ $user->last_login_at?->diffForHumans() ?? 'Just now' }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('account.characters') }}" class="px-4 py-2.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 font-semibold text-xs flex items-center gap-2 transition-colors">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>View Characters ({{ $charCount }})</span>
            </a>
            <a href="{{ route('escrow.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs flex items-center gap-2 transition-colors">
                <i data-lucide="inbox" class="w-4 h-4 text-cyan-400"></i>
                <span>Escrow Inbox</span>
            </a>
            @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('redeem_codes'))
                <a href="{{ route('account.redeem.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs flex items-center gap-2 transition-colors">
                    <i data-lucide="ticket" class="w-4 h-4 text-cyan-400"></i>
                    <span>Redeem Code</span>
                </a>
            @endif
            @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('attendance'))
                <a href="{{ route('attendance.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs flex items-center gap-2 transition-colors">
                    <i data-lucide="calendar-check" class="w-4 h-4 text-amber-400"></i>
                    <span>Daily Rewards</span>
                </a>
            @endif
            @if($user->isStaff())
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 font-semibold text-xs flex items-center gap-2 transition-colors">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Admin Control Panel</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Change Password Form -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 backdrop-blur-sm space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="key" class="w-4 h-4 text-cyan-400"></i>
                <h2 class="text-sm font-bold uppercase tracking-wider text-white">Change Password</h2>
            </div>

            <form method="POST" action="{{ route('account.password') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">New Password</label>
                    <input type="password" name="password" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200">
                </div>
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-md transition-colors">
                    Update Password
                </button>
            </form>
        </div>

        <!-- Change Email Form -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 backdrop-blur-sm space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="mail" class="w-4 h-4 text-blue-400"></i>
                <h2 class="text-sm font-bold uppercase tracking-wider text-white">Update Email</h2>
            </div>

            <form method="POST" action="{{ route('account.email') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200">
                </div>
                <p class="text-[11px] text-slate-500">Used for account recovery and password resets.</p>
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-md transition-colors">
                    Update Email
                </button>
            </form>
        </div>

        <!-- Change Pincode Form -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 backdrop-blur-sm space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="shield-check" class="w-4 h-4 text-purple-400"></i>
                <h2 class="text-sm font-bold uppercase tracking-wider text-white">Security Pincode</h2>
            </div>

            <form method="POST" action="{{ route('account.pincode') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-slate-400 mb-1">4-Digit Security PIN</label>
                    <input type="password" name="pincode" maxlength="4" pattern="\d{4}" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200"
                        placeholder="e.g. 1234">
                </div>
                <p class="text-[11px] text-slate-500">In-game character selection security code.</p>
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs shadow-md transition-colors">
                    Update PIN
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

