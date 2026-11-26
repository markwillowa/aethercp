@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ tab: 'profile' }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $character->name }}</h1>
            <p class="text-xs text-slate-400 mt-1">
                {{ $character->job_name }} • Base Lv. {{ $character->base_level }} / Job Lv. {{ $character->job_level }} •
                Cash Points: <span class="text-cyan-400 font-bold">{{ number_format($cash_points) }} CP</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase {{ (int)$character->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-300' }}">
                {{ (int)$character->online > 0 ? 'Online' : 'Offline' }}
            </span>
            <a href="{{ route('account.characters') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 inline-flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>My Characters</span>
            </a>
        </div>
    </div>

    @if((int)$character->online > 0)
        <div class="bg-amber-950/40 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-200 flex items-center gap-2.5">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400 shrink-0"></i>
            <span>This character is currently <strong>Online</strong>. Please log out of the game before using character services.</span>
        </div>
    @endif

    @if($active_lock)
        <div class="bg-rose-950/40 border border-rose-500/30 rounded-2xl p-4 text-xs text-rose-200 flex items-center justify-between gap-3">
            <div>
                <strong>Character Locked ({{ $active_lock->lock_scope }}):</strong> {{ $active_lock->reason }}
            </div>
            @if((int)$active_lock->locked_by === (int)auth()->id() && $active_lock->lock_scope === 'services')
                <form method="POST" action="{{ route('account.characters.services.lock', $character->char_id) }}">
                    @csrf
                    <input type="hidden" name="action" value="unlock">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-slate-950 font-bold cursor-pointer">Unlock</button>
                </form>
            @endif
        </div>
    @endif

    @if($deletion_request && $deletion_request->status === 'pending')
        <div class="bg-amber-950/40 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-200 flex items-center justify-between gap-3">
            <div>
                <strong>Scheduled for Deletion:</strong> {{ $deletion_request->scheduled_delete_at->format('M d, Y H:i') }}
            </div>
            <form method="POST" action="{{ route('account.characters.services.deletion.cancel', $character->char_id) }}">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-slate-950 font-bold cursor-pointer">Cancel Deletion</button>
            </form>
        </div>
    @endif

    <div class="flex flex-wrap gap-2 border-b border-slate-800 pb-3 text-xs font-semibold">
        <button type="button" @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 border border-slate-800'" class="px-4 py-2 rounded-xl cursor-pointer">Character Profile</button>
        <button type="button" @click="tab = 'services'" :class="tab === 'services' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 border border-slate-800'" class="px-4 py-2 rounded-xl cursor-pointer">Self-Services</button>
        <button type="button" @click="tab = 'privacy'" :class="tab === 'privacy' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 border border-slate-800'" class="px-4 py-2 rounded-xl cursor-pointer">Profile Customization &amp; Privacy</button>
        <button type="button" @click="tab = 'activity'" :class="tab === 'activity' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 border border-slate-800'" class="px-4 py-2 rounded-xl cursor-pointer">Service History</button>
    </div>

    <!-- Profile Tab -->
    <div x-show="tab === 'profile'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-2">Overview &amp; Appearance</h2>
            <div class="space-y-2 text-slate-300">
                <div class="flex justify-between"><span class="text-slate-400">Zeny</span><span class="font-bold text-amber-400">{{ number_format($character->zeny) }} Z</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Guild</span><span class="font-semibold">{{ $character->guild_name ?: 'None' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Location</span><span class="font-mono">{{ $character->last_map }} ({{ $character->last_x }}, {{ $character->last_y }})</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Hair Style / Color</span><span>#{{ $character->hair }} / #{{ $character->hair_color }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Clothes Color</span><span>#{{ $character->clothes_color }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Account Gender</span><span>{{ ($account->sex ?? 'M') === 'F' ? 'Female (F)' : 'Male (M)' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Last Login</span><span>{{ $character->last_login ?: 'Never' }}</span></div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-2">Attributes</h2>
            <div class="grid grid-cols-3 gap-3">
                @foreach(['str', 'agi', 'vit', 'int', 'dex', 'luk'] as $stat)
                    <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-slate-400 block">{{ $stat }}</span>
                        <span class="text-base font-bold text-white">{{ $character->{$stat} }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-800 text-slate-300">
                <span>Available Status Points: <strong class="text-cyan-400">{{ $character->status_point }}</strong></span>
                <span>Skill Points: <strong class="text-cyan-400">{{ $character->skill_point }}</strong></span>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3">
            <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-2">Currently Equipped</h2>
            @forelse($equipped as $eq)
                <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-2.5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-white block">{{ $eq->refine > 0 ? "+{$eq->refine} " : '' }}{{ $eq->item_name }}</span>
                        <span class="text-[10px] text-cyan-400">{{ $eq->equip_slot_name }} • #{{ $eq->nameid }}</span>
                    </div>
                </div>
            @empty
                <p class="text-slate-500">No items currently equipped.</p>
            @endforelse
        </div>
    </div>

    <!-- Self-Services Tab -->
    <div x-show="tab === 'services'" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        @if($settings['rename_enabled'])
            <form method="POST" action="{{ route('account.characters.services.rename', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-rename">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Character Rename</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['rename_cost']) }} CP</span>
                </div>
                <input type="text" name="new_name" required minlength="4" maxlength="23" placeholder="New character name (4-23 chars)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Rename Character</button>
            </form>
        @endif

        @if($settings['appearance_enabled'])
            <form method="POST" action="{{ route('account.characters.services.appearance', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-appearance">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Appearance Change</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['appearance_cost']) }} CP</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Hair (0-{{ $settings['appearance_max_hair'] }})</label>
                        <input type="number" name="hair" value="{{ $character->hair }}" min="0" max="{{ $settings['appearance_max_hair'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Hair Color (0-{{ $settings['appearance_max_hair_color'] }})</label>
                        <input type="number" name="hair_color" value="{{ $character->hair_color }}" min="0" max="{{ $settings['appearance_max_hair_color'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Clothes (0-{{ $settings['appearance_max_clothes_color'] }})</label>
                        <input type="number" name="clothes_color" value="{{ $character->clothes_color }}" min="0" max="{{ $settings['appearance_max_clothes_color'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Update Appearance</button>
            </form>
        @endif

        @if($settings['stat_reset_enabled'])
            <form method="POST" action="{{ route('account.characters.services.stat-reset', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Reset all character attributes?');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-stat">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Stat Reset</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['stat_reset_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Resets STR/AGI/VIT/INT/DEX/LUK to 1 and refunds all spent status points. Min Level: {{ $settings['stat_reset_min_level'] }}.</p>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold cursor-pointer">Reset Stats</button>
            </form>
        @endif

        @if($settings['skill_reset_enabled'])
            <form method="POST" action="{{ route('account.characters.services.skill-reset', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Reset all learned skills?');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-skill">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Skill Reset</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['skill_reset_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Resets learned skills and returns all spent skill points. Min Level: {{ $settings['skill_reset_min_level'] }}.</p>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold cursor-pointer">Reset Skills</button>
            </form>
        @endif

        @if($settings['gender_enabled'])
            <form method="POST" action="{{ route('account.characters.services.gender', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Change account gender? This affects all characters on your game account.');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-gender">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Account Gender Change</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['gender_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Note: rAthena stores gender at the account level. All characters on your account must be offline.</p>
                <div class="flex items-center gap-3">
                    <select name="gender" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <option value="M" {{ ($account->sex ?? 'M') === 'M' ? 'selected' : '' }}>Male (M)</option>
                        <option value="F" {{ ($account->sex ?? 'M') === 'F' ? 'selected' : '' }}>Female (F)</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Change Gender</button>
                </div>
            </form>
        @endif

        @if($settings['lock_enabled'] || $settings['deletion_enabled'])
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white">Security &amp; Deletion</h3>
                @if($settings['lock_enabled'] && !$active_lock)
                    <form method="POST" action="{{ route('account.characters.services.lock', $character->char_id) }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="action" value="lock">
                        <input type="text" name="reason" placeholder="Optional lock note..." class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold cursor-pointer">Self-Lock</button>
                    </form>
                @endif
                @if($settings['deletion_enabled'] && (!$deletion_request || $deletion_request->status !== 'pending'))
                    <form method="POST" action="{{ route('account.characters.services.deletion.request', $character->char_id) }}" onsubmit="return confirm('Schedule this character for deletion?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-950/60 hover:bg-rose-900 text-rose-300 border border-rose-500/30 font-bold cursor-pointer">
                            Request Deletion ({{ $settings['deletion_wait_hours'] }}h Wait)
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <!-- Privacy & Customization Tab -->
    <div x-show="tab === 'privacy'" style="display: none;" class="max-w-3xl text-xs">
        <form method="POST" action="{{ route('account.characters.privacy', $character->char_id) }}" enctype="multipart/form-data" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-5">
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

            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-white">Profile Customization &amp; Privacy</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Control what appears on your public character profile.</p>
                </div>
                <span class="text-[10px] px-2.5 py-1 rounded-full font-bold uppercase {{ ($privacy->is_public ?? true) ? 'text-emerald-400 bg-emerald-950/60 border border-emerald-500/30' : 'text-amber-400 bg-amber-950/60 border border-amber-500/30' }}">
                    {{ ($privacy->is_public ?? true) ? 'Public' : 'Private' }}
                </span>
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2.5">Display &amp; Visibility</span>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-slate-300">
                    @foreach($privacyFields as $pField => $pLabel)
                        <label class="flex items-center gap-2 cursor-pointer hover:text-white bg-slate-950/60 border border-slate-800/80 rounded-xl px-3 py-2">
                            <input type="checkbox" name="{{ $pField }}" value="1" {{ ($privacy->$pField ?? true) ? 'checked' : '' }} class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-0">
                            <span>{{ $pLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Character Avatar / Portrait</span>
                <input type="url" name="avatar_url" value="{{ !empty($privacy->avatar_path) && filter_var($privacy->avatar_path, FILTER_VALIDATE_URL) ? $privacy->avatar_path : '' }}" placeholder="Image URL (e.g. from Google or RateMyServer)..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs placeholder:text-slate-600 focus:border-cyan-500">
                <div class="flex items-center gap-3">
                    <label class="text-xs text-slate-400 shrink-0">Or Upload:</label>
                    <input type="file" name="avatar_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700">
                </div>
                @if(!empty($privacy->avatar_path))
                    <label class="flex items-center gap-2 text-xs text-amber-400 cursor-pointer pt-1">
                        <input type="checkbox" name="remove_avatar" value="1" class="rounded bg-slate-900 border-slate-700 text-amber-500">
                        <span>Reset to default job portrait</span>
                    </label>
                @endif
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Adventurer Bio / Motto</span>
                <input type="text" name="bio" value="{{ $privacy->bio ?? '' }}" placeholder="Short adventurer motto or story..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs placeholder:text-slate-600 focus:border-cyan-500">
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold transition-colors cursor-pointer">
                Save Profile &amp; Privacy
            </button>
        </form>
    </div>

    <!-- Activity Tab -->
    <div x-show="tab === 'activity'" style="display: none;" class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden text-xs">
        <div class="px-5 py-3.5 bg-slate-950/60 border-b border-slate-800">
            <h3 class="text-sm font-bold text-white">Recent Service History</h3>
        </div>
        <div class="divide-y divide-slate-800/50">
            @forelse($logs as $log)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-cyan-300">{{ $log->action_type }}</span>
                        <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] {{ $log->result === 'success' ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400' }}">{{ $log->result }}</span>
                        @if($log->service_cost > 0)
                            <span class="text-slate-400 ml-2">-{{ number_format($log->service_cost) }} CP</span>
                        @endif
                    </div>
                    <span class="text-slate-500">{{ $log->created_at->format('M d, Y H:i') }}</span>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-slate-500">No service activity recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
