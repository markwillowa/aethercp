@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('forum.index') }}" class="hover:text-cyan-400">Forum</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        <span class="text-slate-200 font-semibold">Character Identity Settings</span>
    </nav>

    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6 sm:p-8 space-y-6"
         x-data="{ mode: '{{ old('identity_mode', $profile->identity_mode) }}', selectedChar: '{{ old('selected_char_id', $profile->selected_char_id) }}' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
                <h1 class="text-2xl font-bold text-white">Forum Identity Settings</h1>
                <p class="text-sm text-slate-400 mt-1">Choose how your identity appears across forum discussions, replies, and leaderboards.</p>
            </div>
            <a href="{{ route('forum.profile.show', $user->username) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200">
                <i data-lucide="user" class="w-4 h-4 text-cyan-400"></i>
                <span>View Public Profile</span>
            </a>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Current Forum Preview</div>
            <x-forum.user-identity :identity="$currentIdentity" :show-points="true" />
        </div>

        <form action="{{ route('forum.identity.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="cursor-pointer rounded-xl border p-4 transition-all"
                       :class="mode === 'username' ? 'border-cyan-500 bg-cyan-500/10' : 'border-slate-800 bg-slate-950/50 hover:border-slate-700'">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="identity_mode" value="username" x-model="mode" class="mt-1 text-cyan-500 focus:ring-cyan-500">
                        <div>
                            <div class="text-sm font-bold text-white">A. Aether Username Only</div>
                            <p class="text-xs text-slate-400 mt-1">Display only your Aether Control Panel username ({{ $user->username }}) and reputation badge.</p>
                        </div>
                    </div>
                </label>

                <label class="cursor-pointer rounded-xl border p-4 transition-all"
                       :class="mode === 'character' ? 'border-cyan-500 bg-cyan-500/10' : 'border-slate-800 bg-slate-950/50 hover:border-slate-700'">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="identity_mode" value="character" x-model="mode" class="mt-1 text-cyan-500 focus:ring-cyan-500">
                        <div>
                            <div class="text-sm font-bold text-white">B. Display Selected Ragnarok Character</div>
                            <p class="text-xs text-slate-400 mt-1">Showcase one of your in-game characters (name, job class, levels) alongside your Aether account.</p>
                        </div>
                    </div>
                </label>
            </div>

            <div x-show="mode === 'character'" x-transition class="space-y-3">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Select Your Ragnarok Character</label>

                @if(empty($characters))
                    <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-xs text-amber-200">
                        No Ragnarok characters were found for your game account. Create a character in-game first, or use Aether Username Only mode.
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($characters as $char)
                            <label class="cursor-pointer rounded-xl border p-3.5 flex items-center gap-3 transition-all"
                                   :class="selectedChar == '{{ $char['char_id'] }}' ? 'border-cyan-500 bg-cyan-500/10' : 'border-slate-800 bg-slate-950/50 hover:border-slate-700'">
                                <input type="radio" name="selected_char_id" value="{{ $char['char_id'] }}" x-model="selectedChar" class="text-cyan-500 focus:ring-cyan-500">
                                @if(!empty($char['sprite_url']))
                                    <img src="{{ $char['sprite_url'] }}" alt="{{ $char['name'] }}" class="w-10 h-10 object-contain rounded-lg bg-slate-900 p-1">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-xs font-bold text-cyan-300">
                                        {{ strtoupper(substr($char['name'], 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-white truncate">{{ $char['name'] }}</div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                        <span class="px-1.5 py-0.2 rounded border text-[10px] {{ $char['badge_color'] }}">{{ $char['job_name'] }}</span>
                                        <span>Lv.{{ $char['base_level'] }}/{{ $char['job_level'] }}</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-800">
                <p class="text-xs text-slate-500">Updating your character automatically updates your displayed identity across all your posts while preserving your Aether account ownership.</p>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-sm font-semibold text-white shrink-0">
                    Save Identity
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
