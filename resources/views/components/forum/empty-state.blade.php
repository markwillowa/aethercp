@props(['icon' => 'message-square', 'title' => 'Nothing here yet', 'description' => 'Check back later or start a new discussion.'])
<div class="rounded-2xl border border-slate-800/80 bg-slate-900/50 p-10 text-center">
    <div class="mx-auto w-12 h-12 rounded-2xl bg-slate-800/80 border border-slate-700/70 flex items-center justify-center text-slate-400 mb-3">
        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
    </div>
    <h3 class="text-base font-semibold text-slate-200">{{ $title }}</h3>
    <p class="text-sm text-slate-400 mt-1 max-w-md mx-auto">{{ $description }}</p>
    @if(isset($action))
        <div class="mt-4">{{ $action }}</div>
    @endif
</div>
