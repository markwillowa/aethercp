@extends('layouts.app')

@section('content')
<div class="payon-container py-12">
    <div class="max-w-md mx-auto payon-scroll-panel p-7 space-y-5">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-md bg-[#263D2C] border border-[#A88950] flex items-center justify-center mx-auto text-[#FFF8E8]">
                <i data-lucide="shield" class="w-6 h-6 text-[#A88950]"></i>
            </div>
            <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">Account Sign In</h1>
            <p class="text-xs text-[#80633B]">Access your Payon Realm profile, characters &amp; treasury</p>
        </div>

        @if ($errors->any())
            <div class="p-3.5 rounded bg-[#FDE8E8] border border-[#8C2D2D] text-xs text-[#6E1F1F] space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-xs font-bold uppercase tracking-wider text-[#35291F] mb-1">Game Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus class="w-full px-3.5 py-2.5 rounded text-sm" placeholder="Enter your game username">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#35291F] mb-1">Password</label>
                <input id="password" type="password" name="password" required class="w-full px-3.5 py-2.5 rounded text-sm" placeholder="••••••••">
            </div>

            <label class="flex items-center gap-2 text-xs text-[#493322] cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-[#80633B]">
                <span>Remember this device</span>
            </label>

            <button type="submit" class="payon-btn-forest w-full py-2.5">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Enter Kingdom</span>
            </button>
        </form>

        <p class="text-center text-xs text-[#80633B] pt-2 border-t border-[#A88950]/30">
            New to the realm? <a href="{{ route('register') }}" class="text-[#263D2C] font-bold hover:underline">Create an Account</a>
        </p>
    </div>
</div>
@endsection
