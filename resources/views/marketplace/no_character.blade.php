@extends('layouts.app')

@section('title', 'Player Marketplace - Character Required')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-[#1B1322] border-2 border-[#B69A65]/60 rounded-2xl shadow-2xl p-8 text-center space-y-6">
        <div class="w-20 h-20 mx-auto rounded-full bg-[#352643] border border-[#D4AF37]/50 flex items-center justify-center text-[#E5B358] shadow-lg">
            <i data-lucide="shield-alert" class="w-10 h-10"></i>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold font-cinzel text-[#FCE69B] tracking-wide">Midgard Character Required</h1>
            <p class="text-sm text-[#C8BDAA] max-w-lg mx-auto">
                To access the Trade House and escrow items, you must have at least one character created under your game account.
            </p>
        </div>

        <div class="p-4 rounded-xl bg-[#140E1B] border border-[#4A3A5A] text-xs text-[#EDE4D2] max-w-md mx-auto text-left space-y-2">
            <p class="font-bold text-[#E5B358] flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4"></i> How to Get Started:
            </p>
            <ol class="list-decimal list-inside space-y-1 text-slate-300">
                <li>Launch the game client and log in with your credentials.</li>
                <li>Create your novice adventurer in any character slot.</li>
                <li>Return here to trade items directly with other players!</li>
            </ol>
        </div>

        <div class="pt-4 flex justify-center gap-4">
            <a href="{{ route('downloads.index') }}" class="px-6 py-2.5 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow-md hover:brightness-110 transition-all flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                Download Client
            </a>
            <a href="{{ route('home') }}" class="px-6 py-2.5 rounded-xl text-xs font-bold font-cinzel bg-[#251A33] text-[#EDE4D2] border border-[#4A3A5A] hover:bg-[#352643] transition-all">
                Return Home
            </a>
        </div>
    </div>
</div>
@endsection
