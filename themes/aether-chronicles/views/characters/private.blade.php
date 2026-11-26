@extends('layouts.app')

@section('content')
<div class="chronicles-container py-16">
    <div class="max-w-xl mx-auto chronicles-panel-ivy text-center space-y-4 py-10">
        <i data-lucide="lock" class="w-12 h-12 text-[#9E5600] mx-auto"></i>
        <h1 class="font-cinzel text-2xl font-black text-[#4A2200]">Private Adventurer Record</h1>
        <p class="text-xs text-[#795E43]">{{ $charName ?? 'This adventurer' }} has chosen to keep their profile private.</p>
        <a href="{{ route('rankings.index') }}" class="chronicles-btn-wood text-xs py-2 px-5 inline-flex w-auto">Back to Hall of Fame</a>
    </div>
</div>
@endsection
