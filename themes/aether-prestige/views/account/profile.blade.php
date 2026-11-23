@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <!-- Header / Overview Card -->
    <div class="prestige-card-elevated p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-2xl">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#C6A76A] to-[#D8BE8A] flex items-center justify-center text-2xl font-black text-[#101725] shadow-xl">
                {{ strtoupper(substr($user->username, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                        {{ $user->username }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40 font-cinzel">
                        {{ $user->role }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-[#101725] text-[#2DD4BF] border border-[#2DD4BF]/30">
                        Account #{{ $user->account_id }}
                    </span>
                </div>
                <p class="text-xs text-[#788394] mt-1.5 flex items-center gap-3">
                    <span>Registered: <strong class="text-[#AAB3C1]">{{ $user->created_at->format('M d, Y') }}</strong></span>
                    <span>â€¢</span>
                    <span>Last Login: <strong class="text-[#AAB3C1]">{{ $user->last_login_at?->diffForHumans() ?? 'Active session' }}</strong></span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('account.characters') }}" class="prestige-btn-outline text-xs">
                <i data-lucide="users" class="w-4 h-4 text-[#C6A76A]"></i>
                <span>Manage Characters ({{ $charCount }})</span>
            </a>
            @if($user->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="prestige-btn-primary text-xs">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    <span>Admin Control Panel</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Forms & Security Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Change Password Card -->
        <div class="prestige-card p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-white/[0.08] pb-3">
                <i data-lucide="key" class="w-4 h-4 text-[#C6A76A]"></i>
                <h2 class="font-cinzel text-sm font-bold uppercase tracking-wider text-white">Change Password</h2>
            </div>

            <form method="POST" action="{{ route('account.password') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1">Current Password</label>
                    <input type="password" name="current_password" required class="prestige-input text-xs" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1">New Password</label>
                    <input type="password" name="password" required class="prestige-input text-xs" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="prestige-input text-xs" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                </div>
                <button type="submit" class="prestige-btn-primary w-full text-xs py-2.5">
                    <span>Update Security Password</span>
                </button>
            </form>
        </div>

        <!-- Change Email Card -->
        <div class="prestige-card p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-white/[0.08] pb-3">
                <i data-lucide="mail" class="w-4 h-4 text-[#C6A76A]"></i>
                <h2 class="font-cinzel text-sm font-bold uppercase tracking-wider text-white">Email Address</h2>
            </div>

            <form method="POST" action="{{ route('account.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1">Current Email</label>
                    <input type="email" disabled value="{{ $user->email }}" class="prestige-input text-xs opacity-60">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1">New Email Address</label>
                    <input type="email" name="email" required class="prestige-input text-xs" placeholder="new-email@domain.com">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1">Confirm With Password</label>
                    <input type="password" name="password" required class="prestige-input text-xs" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                </div>
                <button type="submit" class="prestige-btn-outline w-full text-xs py-2.5">
                    <span>Update Email Address</span>
                </button>
            </form>
        </div>

        <!-- Security Pincode & Quick Stats -->
        <div class="prestige-card p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-white/[0.08] pb-3">
                <i data-lucide="shield-check" class="w-4 h-4 text-[#C6A76A]"></i>
                <h2 class="font-cinzel text-sm font-bold uppercase tracking-wider text-white">Security & Cash Points</h2>
            </div>

            <div class="bg-[#101725] border border-white/[0.08] rounded-xl p-4 space-y-2">
                <span class="text-[10px] uppercase tracking-wider text-[#788394] font-semibold block">Donation & Kafra Points</span>
                <div class="flex items-center justify-between">
                    <span class="font-cinzel text-xl font-bold text-[#C6A76A]">0 Cash Points</span>
                    <a href="{{ route('donations.index') }}" class="text-[11px] text-[#2DD4BF] hover:underline font-bold">Donate â†’</a>
                </div>
            </div>

            <form method="POST" action="{{ route('account.pincode') }}" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1">Character 4-Digit Pincode</label>
                    <input type="password" name="pincode" maxlength="4" class="prestige-input text-xs font-mono tracking-widest text-center" placeholder="â€¢â€¢â€¢â€¢">
                </div>
                <button type="submit" class="prestige-btn-outline w-full text-xs py-2.5">
                    <span>Update Character Pincode</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

