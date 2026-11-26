@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Character Service Settings</h1>
            <p class="text-xs text-slate-400 mt-1">Configure Cash Point costs, cooldowns, level requirements, and limits for player self-services.</p>
        </div>
        <a href="{{ route('admin.characters') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 inline-flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Characters</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.characters.settings.update') }}" class="space-y-6 text-xs">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Rename -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h2 class="text-sm font-bold text-white">Character Rename</h2>
                    <label class="inline-flex items-center gap-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" name="rename_enabled" value="1" {{ $settings['rename_enabled'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Enabled</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Cash Points Cost</label>
                        <input type="number" name="rename_cost" value="{{ $settings['rename_cost'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Cooldown (Hours)</label>
                        <input type="number" name="rename_cooldown_hours" value="{{ $settings['rename_cooldown_hours'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Min Base Level</label>
                        <input type="number" name="rename_min_level" value="{{ $settings['rename_min_level'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Max Base Level</label>
                        <input type="number" name="rename_max_level" value="{{ $settings['rename_max_level'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Blocked Words (comma-separated)</label>
                    <input type="text" name="rename_blocked_words" value="{{ $settings['rename_blocked_words'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                </div>
            </div>

            <!-- Appearance -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h2 class="text-sm font-bold text-white">Appearance Change</h2>
                    <label class="inline-flex items-center gap-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" name="appearance_enabled" value="1" {{ $settings['appearance_enabled'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Enabled</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Cash Points Cost</label>
                        <input type="number" name="appearance_cost" value="{{ $settings['appearance_cost'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Cooldown (Hours)</label>
                        <input type="number" name="appearance_cooldown_hours" value="{{ $settings['appearance_cooldown_hours'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Max Hair Style ID</label>
                        <input type="number" name="appearance_max_hair" value="{{ $settings['appearance_max_hair'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Max Hair Color ID</label>
                        <input type="number" name="appearance_max_hair_color" value="{{ $settings['appearance_max_hair_color'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Max Clothes Color ID</label>
                        <input type="number" name="appearance_max_clothes_color" value="{{ $settings['appearance_max_clothes_color'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>
            </div>

            <!-- Stat Reset -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h2 class="text-sm font-bold text-white">Stat Reset</h2>
                    <label class="inline-flex items-center gap-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" name="stat_reset_enabled" value="1" {{ $settings['stat_reset_enabled'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Enabled</span>
                    </label>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Cash Points Cost</label>
                        <input type="number" name="stat_reset_cost" value="{{ $settings['stat_reset_cost'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Cooldown (Hours)</label>
                        <input type="number" name="stat_reset_cooldown_hours" value="{{ $settings['stat_reset_cooldown_hours'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Min Base Level</label>
                        <input type="number" name="stat_reset_min_level" value="{{ $settings['stat_reset_min_level'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>
            </div>

            <!-- Skill Reset -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h2 class="text-sm font-bold text-white">Skill Reset</h2>
                    <label class="inline-flex items-center gap-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" name="skill_reset_enabled" value="1" {{ $settings['skill_reset_enabled'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Enabled</span>
                    </label>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Cash Points Cost</label>
                        <input type="number" name="skill_reset_cost" value="{{ $settings['skill_reset_cost'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Cooldown (Hours)</label>
                        <input type="number" name="skill_reset_cooldown_hours" value="{{ $settings['skill_reset_cooldown_hours'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Min Base Level</label>
                        <input type="number" name="skill_reset_min_level" value="{{ $settings['skill_reset_min_level'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>
            </div>

            <!-- Gender Change -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-white">Account Gender Change</h2>
                        <p class="text-[11px] text-slate-400">Applies at account level (login.sex) across all characters.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" name="gender_enabled" value="1" {{ $settings['gender_enabled'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Enabled</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Cash Points Cost</label>
                        <input type="number" name="gender_cost" value="{{ $settings['gender_cost'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Cooldown (Hours)</label>
                        <input type="number" name="gender_cooldown_hours" value="{{ $settings['gender_cooldown_hours'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>
            </div>

            <!-- Deletion & Lock -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h2 class="text-sm font-bold text-white">Deletion, Recovery &amp; Lock</h2>
                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center gap-1.5 text-slate-300 cursor-pointer">
                            <input type="checkbox" name="deletion_enabled" value="1" {{ $settings['deletion_enabled'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                            <span>Deletion</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 text-slate-300 cursor-pointer">
                            <input type="checkbox" name="lock_enabled" value="1" {{ $settings['lock_enabled'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                            <span>Self-Lock</span>
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 items-end">
                    <div>
                        <label class="block text-slate-400 mb-1">Deletion Waiting Period (Hours)</label>
                        <input type="number" name="deletion_wait_hours" value="{{ $settings['deletion_wait_hours'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <label class="inline-flex items-center gap-2 text-slate-300 py-2 cursor-pointer">
                        <input type="checkbox" name="deletion_allow_recovery" value="1" {{ $settings['deletion_allow_recovery'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Allow Player Recovery Requests</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg shadow-cyan-500/20 cursor-pointer">
                Save Service Settings
            </button>
        </div>
    </form>
</div>
@endsection
