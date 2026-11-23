@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-lg space-y-6">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#1B2432] to-[#222D3C] border border-[#C6A76A]/50 items-center justify-center shadow-xl">
                <i data-lucide="sparkles" class="w-6 h-6 text-[#C6A76A]"></i>
            </div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Forge Your Legacy
            </h1>
            <p class="text-xs text-[#788394]">Register a new game account and embark on your journey in {{ config('aether.server_name', 'MYTHERA') }}.</p>
        </div>

        <!-- Registration Card -->
        <div class="prestige-card-elevated p-6 sm:p-8 shadow-2xl">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1.5 font-cinzel">
                        Desired Username
                    </label>
                    <div class="relative">
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                               class="prestige-input pl-10" placeholder="Letters and numbers only (4-23 chars)">
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
                        Email Address
                    </label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="prestige-input pl-10" placeholder="For account recovery & notifications">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#788394]">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-[11px] text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1.5 font-cinzel">
                            Password
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

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-[#AAB3C1] mb-1.5 font-cinzel">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" required
                                   class="prestige-input pl-10" placeholder="••••••••">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#788394]">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-start gap-2.5 cursor-pointer text-xs text-[#AAB3C1]">
                        <input type="checkbox" name="terms" required class="mt-0.5 rounded bg-[#101725] border-white/20 text-[#C6A76A] focus:ring-0">
                        <span>I accept the server rules, fair-play policies, and privacy terms.</span>
                    </label>
                </div>

                <div class="pt-3">
                    <button type="submit" class="prestige-btn-primary w-full py-3">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Create Adventurer Account</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-white/[0.08] text-center text-xs text-[#788394]">
                Already possess a verified account?
                <a href="{{ route('login') }}" class="text-[#C6A76A] font-bold hover:underline ml-1">Sign In Here</a>
            </div>
        </div>
    </div>
</div>
@endsection

