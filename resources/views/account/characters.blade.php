@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">My Characters</h1>
            <p class="text-xs text-slate-400 mt-1">Characters linked to game account #{{ $user->account_id }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('character_creation'))
                <a href="{{ route('account.characters.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-md shadow-cyan-500/20 inline-flex items-center gap-1.5 transition-all">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Create New Character</span>
                </a>
            @endif
            <a href="{{ route('account.profile') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-medium">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Account</span>
            </a>
        </div>
    </div>

    @if(count($characters) === 0)
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-12 text-center text-slate-400 space-y-3">
            <i data-lucide="user-x" class="w-12 h-12 mx-auto text-slate-600"></i>
            <h3 class="text-lg font-bold text-slate-200">No Characters Created</h3>
            <p class="text-xs text-slate-400">Create your first adventurer online or launch your Ragnarok game client.</p>
            @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('character_creation'))
                <div class="pt-2">
                    <a href="{{ route('account.characters.create') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 inline-flex items-center gap-2 shadow-lg shadow-cyan-500/20">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Create Character Online</span>
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($characters as $char)
                @php
                    $charGuild = (!empty($char->guild_id) && $char->guild_id > 0)
                        ? \Illuminate\Support\Facades\DB::connection('ragnarok')->table('guild')->where('guild_id', $char->guild_id)->select('guild_id', 'name', 'emblem_len')->first()
                        : null;
                    $spriteUrl = \App\Services\Emulator\JobHelper::getSpriteUrl((int) $char->class, (string) ($char->sex ?? 'M'));
                    $cPrivacy = \App\Models\PlayerProfileSetting::forCharacter((int) $char->char_id, (int) $user->account_id);
                    $displayAvatar = !empty($cPrivacy->avatar_path) ? $cPrivacy->avatar_path : $spriteUrl;
                @endphp
                <div class="bg-slate-900/70 border border-slate-800 hover:border-slate-700/80 rounded-2xl p-6 backdrop-blur-sm transition-all duration-200 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-14 h-14 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden">
                                @if($displayAvatar)
                                    <img src="{{ $displayAvatar }}" alt="{{ \App\Services\Emulator\JobHelper::getName($char->class) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="w-full h-full bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-lg font-black text-slate-950">
                                        {{ strtoupper(substr($char->name, 0, 1)) }}
                                    </span>
                                @endif
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

                    <div x-data="{ openCustom: false }" class="pt-2 border-t border-slate-800/60">
                        <button type="button" @click="openCustom = !openCustom" class="w-full flex items-center justify-between text-[11px] font-semibold text-slate-400 hover:text-cyan-400 transition-colors py-1">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="sliders" class="w-3.5 h-3.5 text-cyan-400"></i>
                                <span>Profile Customization &amp; Privacy</span>
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] px-2 py-0.2 rounded font-mono {{ $cPrivacy->is_public ? 'text-emerald-400 bg-emerald-950/60' : 'text-amber-400 bg-amber-950/60' }}">
                                    {{ $cPrivacy->is_public ? 'Public' : 'Private' }}
                                </span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5" :class="openCustom ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <form x-show="openCustom" style="display: none;" method="POST" action="{{ route('account.characters.privacy', $char->char_id) }}" enctype="multipart/form-data" class="mt-3 space-y-3 text-[11px] bg-slate-950/80 p-3.5 rounded-xl border border-slate-800/80">
                            @csrf
                            @php
                                $acctRankingsEnabled = app(\App\Services\Module\ModuleManager::class)->isEnabled('rankings');
                                $privacyFields = [
                                    'is_public' => 'Public Profile',
                                    'show_char_info' => 'Basic Info',
                                    'show_equipments' => 'Equipments',
                                    'show_stats' => 'Stats & Attributes',
                                    'show_zeny' => 'Zeny Balance',
                                    'show_guild' => 'Guild Standing',
                                    'show_last_login' => 'Last Login Time',
                                ];
                                if ($acctRankingsEnabled) {
                                    $privacyFields['show_badges'] = 'Earned Badges';
                                    $privacyFields['show_rankings'] = 'Leaderboard Standings';
                                    $privacyFields['show_achievements'] = 'Achievements';
                                }
                            @endphp

                            <!-- Visibility Checkboxes -->
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Display &amp; Visibility</span>
                                <div class="grid grid-cols-2 gap-2 text-slate-300">
                                    @foreach($privacyFields as $pField => $pLabel)
                                        <label class="flex items-center gap-2 cursor-pointer hover:text-white">
                                            <input type="checkbox" name="{{ $pField }}" value="1" {{ ($cPrivacy->$pField ?? true) ? 'checked' : '' }} class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-0">
                                            <span>{{ $pLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Avatar Customization -->
                            <div class="border-t border-slate-800/80 pt-2.5 space-y-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Character Avatar / Portrait</span>
                                <div class="space-y-1.5">
                                    <input type="url" name="avatar_url" value="{{ !empty($cPrivacy->avatar_path) && filter_var($cPrivacy->avatar_path, FILTER_VALIDATE_URL) ? $cPrivacy->avatar_path : '' }}" placeholder="Image URL (e.g. from Google or RateMyServer)..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1 text-white text-xs placeholder:text-slate-600 focus:border-cyan-500">
                                    <div class="flex items-center gap-2">
                                        <label class="text-[10px] text-slate-400 shrink-0">Or Upload:</label>
                                        <input type="file" name="avatar_file" accept="image/*" class="w-full text-[10px] text-slate-400 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700">
                                    </div>
                                    @if(!empty($cPrivacy->avatar_path))
                                        <label class="flex items-center gap-1.5 text-[10px] text-amber-400 cursor-pointer pt-0.5">
                                            <input type="checkbox" name="remove_avatar" value="1" class="rounded bg-slate-900 border-slate-700 text-amber-500">
                                            <span>Reset to default job portrait</span>
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <!-- Bio Input -->
                            <div class="border-t border-slate-800/80 pt-2.5 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Adventurer Bio / Motto</span>
                                <input type="text" name="bio" value="{{ $cPrivacy->bio }}" placeholder="Short adventurer motto or story..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1 text-white text-xs placeholder:text-slate-600 focus:border-cyan-500">
                            </div>

                            <button type="submit" class="w-full py-1.5 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold transition-colors">
                                Save Profile &amp; Privacy
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

