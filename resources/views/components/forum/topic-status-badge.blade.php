@props(['topic'])
<div class="inline-flex flex-wrap items-center gap-1.5">
    @if($topic->is_pinned)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-500/15 border border-amber-500/30 text-amber-300 text-[10px] font-bold uppercase tracking-wider">
            <i data-lucide="pin" class="w-3 h-3"></i> Pinned
        </span>
    @endif
    @if($topic->is_announcement)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-500/15 border border-purple-500/30 text-purple-300 text-[10px] font-bold uppercase tracking-wider">
            <i data-lucide="megaphone" class="w-3 h-3"></i> Official
        </span>
    @endif
    @if($topic->is_solved)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-[10px] font-bold uppercase tracking-wider">
            <i data-lucide="check-circle-2" class="w-3 h-3"></i> Solved
        </span>
    @endif
    @if($topic->is_approved_guide)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-cyan-500/15 border border-cyan-500/30 text-cyan-300 text-[10px] font-bold uppercase tracking-wider">
            <i data-lucide="book-open" class="w-3 h-3"></i> Guide
        </span>
    @endif
    @if($topic->is_locked)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
            <i data-lucide="lock" class="w-3 h-3"></i> Locked
        </span>
    @endif
</div>
