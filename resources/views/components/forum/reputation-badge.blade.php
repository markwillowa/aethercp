@props([
    'level' => ['name' => 'Newcomer', 'badge_color' => '#64748b', 'badge_icon' => 'sprout', 'badge_style' => 'subtle'],
    'points' => 0,
    'showPoints' => false,
])
@php
    $color = $level['badge_color'] ?? '#64748b';
    $icon = $level['badge_icon'] ?? 'award';
    $style = $level['badge_style'] ?? 'solid';
    $name = $level['name'] ?? 'Newcomer';
@endphp
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold tracking-wide border transition-all"
      style="color: {{ $color }}; border-color: {{ $color }}55; background-color: {{ $color }}18; {{ $style === 'glow' ? 'box-shadow: 0 0 10px ' . $color . '35;' : '' }}">
    <i data-lucide="{{ $icon }}" class="w-3 h-3 shrink-0"></i>
    <span>{{ $name }}</span>
    @if($showPoints)
        <span class="opacity-80 font-mono text-[10px]">({{ number_format($points) }})</span>
    @endif
</span>
