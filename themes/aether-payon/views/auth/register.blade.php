@extends('layouts.app')

@section('content')
<div class="payon-container py-12">
    <div class="max-w-md mx-auto payon-scroll-panel p-7 space-y-5">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-md bg-[#493322] border border-[#A88950] flex items-center justify-center mx-auto text-[#FFF8E8]">
                <i data-lucide="scroll-text" class="w-6 h-6 text-[#A88950]"></i>
            </div>
            <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">Adventurer Registration</h1>
            <p class="text-xs text-[#80633B]">One account for both the game client and Payon web portal</p>
        </div>

        @if ($errors->any())
            <div class="p-3.5 rounded bg-[#FDE8E8] border border-[#8C2D2D] text-xs text-[#6E1F1F] space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-xs font-bold uppercase tracking-wider text-[#35291F] mb-1">Username (Game &amp; Web)</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus class="w-full px-3.5 py-2.5 rounded text-sm" placeholder="Choose a username">
                <p class="text-[11px] text-[#80633B] mt-1">4-23 alphanumeric characters or underscore.</p>
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#35291F] mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full px-3.5 py-2.5 rounded text-sm" placeholder="adventurer@example.com">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#35291F] mb-1">Password</label>
                <input id="password" type="password" name="password" required class="w-full px-3.5 py-2.5 rounded text-sm" placeholder="••••••••">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#35291F] mb-1">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full px-3.5 py-2.5 rounded text-sm" placeholder="••••••••">
            </div>

            <button type="submit" class="payon-btn-gold w-full py-2.5">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Inscribe Account</span>
            </button>
        </form>

        <p class="text-center text-xs text-[#80633B] pt-2 border-t border-[#A88950]/30">
            Already registered? <a href="{{ route('login') }}" class="text-[#263D2C] font-bold hover:underline">Sign In</a>
        </p>
    </div>
</div>
@endsection
