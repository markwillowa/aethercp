@extends('layouts.app')

@section('title', $category->name . ' - Knowledge Base')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto py-2">

    <!-- Breadcrumb & Back Link -->
    <div class="flex items-center justify-between text-xs text-slate-400">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-cyan-400 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('faq.index') }}" class="hover:text-cyan-400 transition-colors">FAQ</a>
            <span>/</span>
            <span class="text-cyan-400 font-medium">{{ $category->name }}</span>
        </div>
        <a href="{{ route('faq.index') }}" class="text-cyan-400 hover:text-cyan-300 flex items-center gap-1 font-semibold">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>All Categories</span>
        </a>
    </div>

    <!-- Category Header Card -->
    <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-cyan-950/40 border border-cyan-500/30 text-cyan-400 flex items-center justify-center shrink-0 shadow-inner">
            <i data-lucide="{{ $category->icon ?: 'folder' }}" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-white uppercase tracking-wide">
                {{ $category->name }}
            </h1>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                {{ $category->description ?: 'Browse published questions and answers in this category.' }}
            </p>
            <span class="inline-block mt-2 text-[10px] font-mono text-cyan-400 bg-cyan-950/40 px-2.5 py-0.5 rounded border border-cyan-500/30 font-semibold">
                {{ $faqs->total() }} {{ Str::plural('Question', $faqs->total()) }} Available
            </span>
        </div>
    </div>

    <!-- Questions Accordion -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 sm:p-6 space-y-4 shadow-xl">
        @if($faqs->count() > 0)
            <div class="space-y-3" x-data="{ activeAccordion: null }">
                @foreach($faqs as $faq)
                    <div class="rounded-xl bg-slate-950 border border-slate-800 overflow-hidden transition-all hover:border-slate-700">
                        <button type="button" @click="activeAccordion = (activeAccordion === {{ $faq->id }} ? null : {{ $faq->id }})"
                                class="w-full p-4 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-slate-900/60 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-cyan-950/40 border border-cyan-500/30 flex items-center justify-center shrink-0 text-cyan-400">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-100 block">{{ $faq->question }}</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                               :class="activeAccordion === {{ $faq->id }} ? 'rotate-180 text-cyan-400' : ''"></i>
                        </button>

                        <div x-show="activeAccordion === {{ $faq->id }}" x-collapse class="p-4 pt-0 border-t border-slate-850 bg-slate-950/80 text-xs text-slate-300 space-y-3 leading-relaxed">
                            <div class="prose prose-invert max-w-none text-xs text-slate-300 pt-3">
                                {!! $faq->answer !!}
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-850 text-[11px] text-slate-400">
                                <a href="{{ route('faq.show', $faq->slug) }}" class="text-cyan-400 hover:underline flex items-center gap-1 font-semibold">
                                    <span>Direct Link &amp; Details</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                                <span>Updated {{ $faq->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-3">
                {{ $faqs->links() }}
            </div>
        @else
            <div class="py-12 text-center space-y-2">
                <i data-lucide="inbox" class="w-10 h-10 text-slate-600 mx-auto"></i>
                <p class="text-sm font-bold text-white">No questions published yet</p>
                <p class="text-xs text-slate-400">Check back soon or submit a support ticket if you need guidance.</p>
            </div>
        @endif
    </div>

    <!-- Still Need Help Support CTA -->
    <div class="rounded-2xl p-6 sm:p-8 border border-slate-800 bg-slate-900/90 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center shrink-0 shadow-inner">
                <i data-lucide="life-buoy" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white uppercase tracking-wide">Still need help?</h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    If you cannot find the answer you are looking for, our server support team and game masters are ready to assist.
                </p>
            </div>
        </div>

        @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support'))
            <a href="{{ route('support.index') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold py-2.5 px-6 rounded-xl text-xs uppercase tracking-wider shrink-0 flex items-center gap-2 shadow-lg transition-all">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>Contact Support</span>
            </a>
        @else
            <a href="{{ \App\Models\SiteSetting::get('social_discord', '#') }}" target="_blank" rel="noopener" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold py-2.5 px-6 rounded-xl text-xs uppercase tracking-wider shrink-0 flex items-center gap-2 shadow-lg transition-all">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span>Join Discord Support</span>
            </a>
        @endif
    </div>

</div>
@endsection
