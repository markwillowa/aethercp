@props([
    'identity' => null,
    'showReputation' => true,
    'showPoints' => false,
    'compact' => false,
])
@php
    $username = $identity['username'] ?? 'Member';
    $char = $identity['character'] ?? null;
    $isStaff = !empty($identity['is_staff']);
    $repLevel = $identity['reputation_level'] ?? ['name' => 'Newcomer', 'badge_color' => '#64748b', 'badge_icon' => 'sprout', 'badge_style' => 'subtle'];
    $repPoints = (int) ($identity['reputation_points'] ?? 0);
    $profileUrl = route('forum.profile.show', $username);
@endphp
<div class="flex items-center gap-2.5 min-w-0">
    <a href="{{ $profileUrl }}" class="shrink-0 group">
        @if($char && !empty($char['sprite_url']))
            <div class="{{ $compact ? 'w-8 h-8' : 'w-10 h-10' }} rounded-xl bg-slate-900/90 border border-slate-700/70 flex items-center justify-center overflow-hidden group-hover:border-cyan-400/60 transition-colors">
                <img src="{{ $char['sprite_url'] }}" alt="{{ $char['name'] }}" class="w-full h-full object-contain">
            </div>
        @else
            <div class="{{ $compact ? 'w-8 h-8 text-xs' : 'w-10 h-10 text-sm' }} rounded-xl bg-gradient-to-br from-cyan-500/25 to-blue-600/25 border border-cyan-500/30 flex items-center justify-center font-bold text-cyan-300 group-hover:border-cyan-400 transition-colors">
                {{ strtoupper(substr($char ? $char['name'] : $username, 0, 1)) }}
            </div>
        @endif
    </a>

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ $profileUrl }}" class="font-semibold text-sm text-slate-100 hover:text-cyan-400 transition-colors truncate">
                {{ $char ? $char['name'] : $username }}
            </a>

            @if($isStaff)
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-500/15 border border-amber-500/30 text-amber-300 text-[10px] font-bold uppercase tracking-wider">
                    <i data-lucide="shield-check" class="w-2.5 h-2.5"></i>
                    Staff
                </span>
            @endif

            @if($showReputation)
                <x-forum.reputation-badge :level="$repLevel" :points="$repPoints" :show-points="$showPoints" />
            @endif
        </div>

        @if($char)
            <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-slate-400 mt-0.5">
                <span class="px-1.5 py-0.2 rounded border text-[10px] font-medium {{ $char['badge_color'] }}">
                    {{ $char['job_name'] }}
                </span>
                <span>Lv.{{ $char['base_level'] }}/{{ $char['job_level'] }}</span>
                <span class="text-slate-600">&bull;</span>
                <span class="text-slate-500">@{{ $username }}</span>
            </div>
        @endif
    </div>
</div>
