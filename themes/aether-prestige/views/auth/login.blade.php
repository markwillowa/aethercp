@extends('layouts.app')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md space-y-6">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#1B2432] to-[#222D3C] border border-[#C6A76A]/50 items-center justify-center shadow-xl">
                <i data-lucide="crown" class="w-6 h-6 text-[#C6A76A]"></i>
            </div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Enter The Realm
            </h1>
            <p class="text-xs text-[#788394]">Authenticate with your server credentials to access your control portal.</p>
        </div>

        <!-- Login Card -->
        <div class="prestige-card-elevated p-6 sm:p-8 shadow-2xl">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1.5 font-cinzel">
                        Game Account Username
                    </label>
                    <div class="relative">
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                               class="prestige-input pl-10" placeholder="Enter username">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#788394]">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                    </div>
                    @error('username')
                        <p class="text-[11px] text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1.5 font-cinzel">
                        Security Password
                    </label>
                    <div class="relative">
                        <input type="password" name="password" required
                               class="prestige-input pl-10" placeholder="••••••••">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#788394]">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-[#AAB3C1] hover:text-white transition-colors">
                        <input type="checkbox" name="remember" class="rounded bg-[#101725] border-white/20 text-[#C6A76A] focus:ring-0">
                        <span>Remember session</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="prestige-btn-primary w-full py-3">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Authenticate & Enter</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-white/[0.08] text-center text-xs text-[#788394]">
                Don't have an adventurer account yet?
                <a href="{{ route('register') }}" class="text-[#C6A76A] font-bold hover:underline ml-1">Create Account Free</a>
            </div>
        </div>
    </div>
</div>
@endsection

