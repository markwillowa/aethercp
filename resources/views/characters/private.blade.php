@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-10 text-center space-y-4 shadow-2xl">
        <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center mx-auto">
            <i data-lucide="lock" class="w-8 h-8 text-amber-400"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">{{ $charName }}&rsquo;s Profile is Private</h1>
        <p class="text-xs sm:text-sm text-slate-400 max-w-md mx-auto leading-relaxed">
            This adventurer has restricted public visibility for their character profile. Only public leaderboard entries allowed by server policy are visible.
        </p>
        <div class="pt-2">
            <a href="{{ route('rankings.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Rankings</span>
            </a>
        </div>
    </div>
</div>
@endsection
