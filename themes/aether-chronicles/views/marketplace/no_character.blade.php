@extends('layouts.app')

@section('content')
<div class="chronicles-container py-16">
    <div class="max-w-xl mx-auto chronicles-panel-ivy text-center space-y-4 py-10">
        <i data-lucide="user-plus" class="w-12 h-12 text-[#9E5600] mx-auto"></i>
        <h1 class="font-cinzel text-2xl font-black text-[#4A2200]">Character Required</h1>
        <p class="text-xs text-[#795E43]">You need at least one character on your game account before accessing the Marketplace.</p>
        <a href="{{ route('account.characters') }}" class="chronicles-btn-wood text-xs py-2 px-5 inline-flex w-auto">Go to Adventurer Roster</a>
    </div>
</div>
@endsection
