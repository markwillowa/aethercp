@extends('layouts.app')

@section('content')
<div class="payon-container py-12 md:py-16">
    <div class="max-w-md mx-auto payon-wood-frame overflow-hidden">
        <div class="payon-wood-header">
            <div class="flex items-center gap-2 text-xs uppercase tracking-wider">
                <i data-lucide="compass" class="w-4 h-4" style="color: #E7D8B5;"></i>
                <span>Payon Realm Gatekeeper</span>
            </div>
            <span class="text-[10px] font-mono uppercase tracking-widest px-2 py-0.5 rounded" style="background-color: #263D2C; color: #E7D8B5; border: 1px solid #A88950;">Portal</span>
        </div>

        <div class="p-6 sm:p-7 space-y-5" style="background: linear-gradient(180deg, #FFF8E8 0%, #F2E8CF 100%);">
            <div class="text-center space-y-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mx-auto shadow-sm" style="background-color: #263D2C; border: 1.5px solid #A88950; color: #FFF8E8;">
                    <i data-lucide="shield" class="w-6 h-6" style="color: #E7D8B5;"></i>
                </div>
                <h1 class="font-cinzel text-2xl font-bold" style="color: #35291F;">Account Sign In</h1>
                <p class="text-xs" style="color: #80633B;">Access your Payon Realm profile, characters &amp; treasury</p>
            </div>

            <div class="payon-bamboo-divider">
                <span class="payon-bamboo-knot"></span>
            </div>

            @if ($errors->any())
                <div class="p-3.5 rounded-md text-xs space-y-1" style="background-color: #FDE8E8; border: 1px solid #8C2D2D; color: #6E1F1F;">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-1.5">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: #35291F;">Game Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color: #80633B;">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-md text-sm" placeholder="Enter your game username">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: #35291F;">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color: #80633B;">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               class="w-full pl-10 pr-10 py-2.5 rounded-md text-sm" placeholder="••••••••">
                        <button type="button" @click="show = !show" aria-label="Toggle password visibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" style="color: #80633B;">
                            <i x-show="!show" data-lucide="eye" class="w-4 h-4"></i>
                            <i x-show="show" x-cloak data-lucide="eye-off" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-xs cursor-pointer select-none" style="color: #493322;">
                    <input type="checkbox" name="remember" class="rounded" style="border-color: #80633B;">
                    <span>Remember this device</span>
                </label>

                <button type="submit" class="payon-btn-forest w-full py-3">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Enter Kingdom</span>
                </button>
            </form>

            <p class="text-center text-xs pt-3" style="color: #80633B; border-top: 1px solid rgba(168, 137, 80, 0.35);">
                New to the realm? <a href="{{ route('register') }}" class="font-bold hover:underline" style="color: #263D2C;">Create an Account</a>
            </p>
        </div>
    </div>
</div>
@endsection
