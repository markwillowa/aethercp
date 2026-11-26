@extends('layouts.app')

@section('title', 'Player Marketplace - Character Required')

@section('content')
<div class="prestige-container py-10">
    <div class="prestige-card max-w-2xl mx-auto p-8 text-center space-y-6">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-[#101725] border border-[#C6A76A]/40 flex items-center justify-center text-[#C6A76A]">
            <i data-lucide="shield-alert" class="w-8 h-8"></i>
        </div>

        <div class="space-y-2">
            <h1 class="font-cinzel text-2xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">Midgard Character Required</h1>
            <p class="text-xs text-[#AAB3C1] max-w-md mx-auto leading-relaxed">
                To access the Player Marketplace and escrow items, you must have at least one character created under your game account.
            </p>
        </div>

        <div class="p-4 rounded-xl bg-[#101725] border border-white/[0.08] text-xs text-[#D9DEE7] max-w-md mx-auto text-left space-y-2">
            <p class="font-cinzel font-bold text-[#D8BE8A] flex items-center gap-2 uppercase tracking-wider">
                <i data-lucide="info" class="w-4 h-4"></i> How to Get Started
            </p>
            <ol class="list-decimal list-inside space-y-1 text-[#AAB3C1]">
                <li>Create a character on the web or launch the game client.</li>
                <li>Return here to browse, buy, or consign items with other players.</li>
            </ol>
        </div>

        <div class="pt-2 flex flex-wrap justify-center gap-3">
            @if(Route::has('account.characters.create'))
                <a href="{{ route('account.characters.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider inline-flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Create Character</span>
                </a>
            @endif
            <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-xl bg-[#101725] border border-white/10 text-[#AAB3C1] hover:text-white font-cinzel font-bold text-xs uppercase tracking-wider inline-flex items-center">
                Return Home
            </a>
        </div>
    </div>
</div>
@endsection
