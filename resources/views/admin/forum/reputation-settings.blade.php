@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Reputation Administration Settings</h1>
            <p class="text-xs text-slate-400 mt-1">Configure point awards, event toggles, deletion reversal policy, leaderboard visibility, and level thresholds.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.forum.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700">Forum Boards</a>
            <a href="{{ route('admin.forum.reputation.history') }}" class="px-4 py-2 rounded-xl bg-cyan-500 text-slate-950 text-xs font-bold">Transaction History &amp; Adjustments</a>
        </div>
    </div>

    <!-- Event Point Rules -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <h2 class="text-base font-bold text-white mb-4">Reputation Rules &amp; Policies</h2>
        <form action="{{ route('admin.forum.reputation.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Positive Reaction (+)</label>
                    <input type="number" name="points_positive_reaction" value="{{ $settings['points_positive_reaction'] }}" min="0" max="1000" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Accepted Answer (+)</label>
                    <input type="number" name="points_accepted_answer" value="{{ $settings['points_accepted_answer'] }}" min="0" max="1000" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Approved Useful Guide (+)</label>
                    <input type="number" name="points_approved_guide" value="{{ $settings['points_approved_guide'] }}" min="0" max="1000" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Moderator Recognition (+)</label>
                    <input type="number" name="points_moderator_recognition" value="{{ $settings['points_moderator_recognition'] }}" min="0" max="1000" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-800">
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Enabled Reputation Events</label>
                    @foreach([
                        'positive_reaction' => 'Receive a positive reaction',
                        'accepted_answer' => 'Receive an accepted answer',
                        'approved_guide' => 'Approved useful guide',
                        'moderator_recognition' => 'Moderator recognition',
                    ] as $evKey => $evLabel)
                        <label class="flex items-center gap-2 text-xs text-slate-300">
                            <input type="checkbox" name="enabled_events[]" value="{{ $evKey }}" @checked(in_array($evKey, $settings['enabled_events'], true)) class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                            <span>{{ $evLabel }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Content Deletion Reversal Policy</label>
                        <select name="reversal_policy" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                            <option value="reverse_all" @selected($settings['reversal_policy'] === 'reverse_all')>Reverse earned reputation when content is deleted</option>
                            <option value="keep_earned" @selected($settings['reversal_policy'] === 'keep_earned')>Keep previously earned reputation</option>
                        </select>
                    </div>

                    <label class="flex items-center gap-2 text-xs text-slate-300">
                        <input type="checkbox" name="leaderboard_visible" value="1" @checked($settings['leaderboard_visible']) class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Publicly display Community Reputation Leaderboard</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-sm font-semibold text-white">Save Reputation Settings</button>
            </div>
        </form>
    </div>

    <!-- Reputation Levels & Badges -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <h2 class="text-base font-bold text-white mb-4">Reputation Levels &amp; Badges</h2>
        <form action="{{ route('admin.forum.reputation.levels.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 uppercase border-b border-slate-800">
                        <tr>
                            <th class="py-2 pr-3">Level Name</th>
                            <th class="py-2 pr-3">Min Points</th>
                            <th class="py-2 pr-3">Badge Color</th>
                            <th class="py-2 pr-3">Lucide Icon</th>
                            <th class="py-2">Style</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/70">
                        @foreach($levels as $idx => $lvl)
                            <tr>
                                <td class="py-2.5 pr-3">
                                    <input type="hidden" name="levels[{{ $idx }}][id]" value="{{ $lvl->id }}">
                                    <input type="text" name="levels[{{ $idx }}][name]" value="{{ $lvl->name }}" required class="w-full px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200">
                                </td>
                                <td class="py-2.5 pr-3">
                                    <input type="number" name="levels[{{ $idx }}][min_points]" value="{{ $lvl->min_points }}" min="0" required class="w-28 px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 font-mono">
                                </td>
                                <td class="py-2.5 pr-3">
                                    <input type="text" name="levels[{{ $idx }}][badge_color]" value="{{ $lvl->badge_color }}" required class="w-28 px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 font-mono">
                                </td>
                                <td class="py-2.5 pr-3">
                                    <input type="text" name="levels[{{ $idx }}][badge_icon]" value="{{ $lvl->badge_icon }}" required class="w-32 px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200">
                                </td>
                                <td class="py-2.5">
                                    <select name="levels[{{ $idx }}][badge_style]" class="px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200">
                                        @foreach(['subtle', 'solid', 'outline', 'glow'] as $st)
                                            <option value="{{ $st }}" @selected($lvl->badge_style === $st)>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-500 text-slate-950 text-sm font-bold">Update Levels &amp; Badges</button>
            </div>
        </form>
    </div>
</div>
@endsection
