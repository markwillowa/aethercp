@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">My Characters</h1>
            <p class="text-xs text-slate-400 mt-1">Characters linked to game account #{{ $user->account_id }}</p>
        </div>
        <a href="{{ route('account.profile') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Account</span>
        </a>
    </div>

    @if(count($characters) === 0)
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-12 text-center text-slate-400">
            <i data-lucide="user-x" class="w-12 h-12 mx-auto mb-3 text-slate-600"></i>
            <h3 class="text-lg font-bold text-slate-200">No Characters Created</h3>
            <p class="text-xs text-slate-400 mt-1">Launch your Ragnarok game client to create your first adventurer!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($characters as $char)
                @php
                    $charGuild = (!empty($char->guild_id) && $char->guild_id > 0)
                        ? \Illuminate\Support\Facades\DB::connection('ragnarok')->table('guild')->where('guild_id', $char->guild_id)->select('guild_id', 'name', 'emblem_len')->first()
                        : null;
                    $spriteUrl = \App\Services\Emulator\JobHelper::getSpriteUrl((int) $char->class, (string) ($char->sex ?? 'M'));
                @endphp
                <div class="bg-slate-900/70 border border-slate-800 hover:border-slate-700/80 rounded-2xl p-6 backdrop-blur-sm transition-all duration-200 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-14 h-14 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden">
                                <img src="{{ $spriteUrl }}" alt="{{ \App\Services\Emulator\JobHelper::getName($char->class) }}" class="w-12 h-12 object-contain" onerror="this.style.display='none';">
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('characters.show', $char->name) }}" class="text-lg font-bold text-white hover:text-cyan-400 transition-colors truncate block">{{ $char->name }}</a>
                                <p class="text-xs text-cyan-400 font-semibold">{{ \App\Services\Emulator\JobHelper::getName($char->class) }}</p>
                                @if($charGuild)
                                    <a href="{{ route('guilds.show', $charGuild->guild_id) }}" class="inline-flex items-center gap-1 text-[11px] text-slate-300 hover:text-cyan-300 mt-0.5">
                                        @if(!empty($charGuild->emblem_len) && $charGuild->emblem_len > 0)
                                            <img src="{{ route('guilds.emblem', $charGuild->guild_id) }}" alt="" class="w-3.5 h-3.5 object-contain">
                                        @else
                                            <i data-lucide="shield" class="w-3 h-3 text-cyan-400"></i>
                                        @endif
                                        <span>{{ $charGuild->name }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $char->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                            {{ $char->online > 0 ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs bg-slate-950/60 p-3 rounded-xl border border-slate-800/60">
                        <div>
                            <span class="text-slate-500 block">Base Level</span>
                            <span class="font-bold text-slate-200">{{ $char->base_level }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Job Level</span>
                            <span class="font-bold text-slate-200">{{ $char->job_level }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Zeny</span>
                            <span class="font-bold text-amber-400">{{ number_format($char->zeny) }} Z</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Location</span>
                            <span class="font-bold text-slate-200 truncate">{{ $char->last_map }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between gap-2">
                        <form method="POST" action="{{ route('account.unstuck', $char->char_id) }}">
                            @csrf
                            <button type="submit" {{ $char->online > 0 ? 'disabled' : '' }}
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-1.5"
                                title="Move character to Prontera if stuck">
                                <i data-lucide="compass" class="w-3.5 h-3.5 text-cyan-400"></i>
                                <span>Reset Position</span>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('account.characters.delete', $char->char_id) }}"
                              onsubmit="return confirm('Are you sure you want to permanently delete character {{ $char->name }}? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" {{ $char->online > 0 ? 'disabled' : '' }}
                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-red-950/40 hover:bg-red-900/60 text-red-400 hover:text-red-300 border border-red-800/40 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-1"
                                title="Delete Character">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>

                    @php
                        $cPrivacy = \App\Models\PlayerProfileSetting::forCharacter((int) $char->char_id, (int) $user->account_id);
                    @endphp
                    <div x-data="{ openPrivacy: false }" class="pt-2 border-t border-slate-800/60">
                        <button type="button" @click="openPrivacy = !openPrivacy" class="w-full flex items-center justify-between text-[11px] font-semibold text-slate-400 hover:text-cyan-400 transition-colors">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                                <span>Profile Privacy ({{ $cPrivacy->is_public ? 'Public' : 'Private' }})</span>
                            </span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                        </button>
                        <form x-show="openPrivacy" style="display: none;" method="POST" action="{{ route('account.characters.privacy', $char->char_id) }}" class="mt-3 space-y-2 text-[11px] bg-slate-950/70 p-3 rounded-xl border border-slate-800">
                            @csrf
                            <div class="grid grid-cols-2 gap-1.5 text-slate-300">
                                @foreach([
                                    'is_public' => 'Public Profile',
                                    'show_char_info' => 'Char Info',
                                    'show_rankings' => 'Rankings',
                                    'show_badges' => 'Badges',
                                    'show_achievements' => 'Achievements',
                                    'show_stats' => 'Statistics',
                                    'show_guild' => 'Guild',
                                    'show_last_login' => 'Last Login',
                                ] as $pField => $pLabel)
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" name="{{ $pField }}" value="1" {{ $cPrivacy->$pField ? 'checked' : '' }} class="rounded bg-slate-900 border-slate-700 text-cyan-500">
                                        <span>{{ $pLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <input type="text" name="bio" value="{{ $cPrivacy->bio }}" placeholder="Short profile bio..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1 text-white">
                            <button type="submit" class="w-full py-1.5 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold">Save Privacy</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

