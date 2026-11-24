@extends('layouts.app')

@section('title', 'Frequently Asked Questions & Knowledge Base')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto py-2">

    <!-- Hero Section -->
    <div class="relative overflow-hidden rounded-2xl p-6 sm:p-10 border border-slate-800 bg-slate-900/80 shadow-2xl">
        <div class="relative z-10 text-center max-w-2xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-semibold tracking-wider uppercase">
                <i data-lucide="help-circle" class="w-3.5 h-3.5 text-cyan-400"></i>
                <span>Knowledge Base &amp; FAQ</span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight uppercase">
                Frequently Asked Questions
            </h1>

            <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                Find clear answers to common questions about server gameplay, account security, downloads, rates, and donation privileges.
            </p>

            <!-- Search Console -->
            <form action="{{ route('faq.index') }}" method="GET" class="pt-2 max-w-xl mx-auto">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <div class="relative flex-1" style="position: relative; display: flex; align-items: center;">
                        <div class="search-icon-centered" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10; width: 16px; height: 16px; line-height: 0;">
                            <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px; display: block;"></i>
                        </div>
                        <input type="text" name="q" value="{{ $searchTerm }}" placeholder="Search questions, keywords, commands (e.g. rates, patcher, accounts)..." 
                               style="width: 100%; height: 46px; padding-left: 48px !important; padding-right: 16px !important; display: block;"
                               class="bg-slate-950 border border-slate-700/80 rounded-xl text-slate-100 placeholder:text-slate-500 text-xs sm:text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all shadow-inner">
                    </div>
                    <button type="submit" 
                            style="height: 46px; padding: 0 24px;"
                            class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 text-xs sm:text-sm font-bold rounded-xl shadow-lg flex items-center justify-center gap-2 shrink-0 cursor-pointer transition-all">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Search</span>
                    </button>
                    @if($searchTerm)
                        <a href="{{ route('faq.index') }}" 
                           style="height: 46px; padding: 0 16px;"
                           class="bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-center gap-1.5 shrink-0 transition-colors" title="Reset Search">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>Clear</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Ambient Glow -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- If Search is active: Search Results -->
    @if($searchResults !== null)
        <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 sm:p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <i data-lucide="search" class="w-5 h-5 text-cyan-400"></i>
                    <h2 class="text-sm font-bold text-white tracking-wide uppercase">
                        Search Results for "{{ $searchTerm }}" ({{ $searchResults->total() }})
                    </h2>
                </div>
                <a href="{{ route('faq.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Clear Search</span>
                </a>
            </div>

            @if($searchResults->count() > 0)
                <div class="space-y-3" x-data="{ activeAccordion: null }">
                    @foreach($searchResults as $faq)
                        <div class="rounded-xl bg-slate-950 border border-slate-800 overflow-hidden transition-all hover:border-slate-700">
                            <button type="button" @click="activeAccordion = (activeAccordion === {{ $faq->id }} ? null : {{ $faq->id }})"
                                    class="w-full p-4 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-slate-900/60 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-cyan-950/40 border border-cyan-500/30 flex items-center justify-center shrink-0 text-cyan-400">
                                        <i data-lucide="help-circle" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs sm:text-sm font-bold text-slate-100 block">{{ $faq->question }}</span>
                                        @if($faq->category)
                                            <span class="text-[10px] text-cyan-400 font-medium">{{ $faq->category->name }}</span>
                                        @endif
                                    </div>
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
                    {{ $searchResults->links() }}
                </div>
            @else
                <div class="py-10 text-center space-y-2">
                    <i data-lucide="search-x" class="w-10 h-10 text-slate-600 mx-auto"></i>
                    <p class="text-sm font-bold text-white">No answers matched your query</p>
                    <p class="text-xs text-slate-400">Try checking your spelling or search for alternative keywords.</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Categories Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="folder" class="w-4 h-4 text-cyan-400"></i>
                <span>Explore by Category</span>
            </h2>
            <span class="text-xs text-slate-400 font-mono">{{ $categories->count() }} Categories</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categories as $cat)
                <a href="{{ route('faq.category', $cat->slug) }}" 
                   class="group p-5 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/50 hover:bg-slate-850 transition-all flex flex-col justify-between shadow-lg">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-950/40 border border-cyan-500/30 text-cyan-400 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:border-cyan-400 transition-all shadow-inner">
                            <i data-lucide="{{ $cat->icon ?: 'folder' }}" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white group-hover:text-cyan-300 transition-colors">
                                {{ $cat->name }}
                            </h3>
                            <p class="text-xs text-slate-400 line-clamp-2 mt-1 leading-relaxed">
                                {{ $cat->description ?: 'Browse questions and official guides in this category.' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 mt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-mono text-slate-400">
                        <span>{{ $cat->published_faqs_count }} {{ Str::plural('Question', $cat->published_faqs_count) }}</span>
                        <span class="text-cyan-400 group-hover:translate-x-1 transition-transform flex items-center gap-1 font-semibold">
                            <span>Browse</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Popular Questions Accordion -->
    @if($featuredFaqs->count() > 0)
        <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 sm:p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="star" class="w-4 h-4 text-cyan-400"></i>
                    <span>Popular &amp; Frequently Asked Questions</span>
                </h2>
                <span class="text-xs text-slate-400 font-mono">Top Community Topics</span>
            </div>

            <div class="space-y-3" x-data="{ activeAccordion: null }">
                @foreach($featuredFaqs as $faq)
                    <div class="rounded-xl bg-slate-950 border border-slate-800 overflow-hidden transition-all hover:border-slate-700">
                        <button type="button" @click="activeAccordion = (activeAccordion === {{ $faq->id }} ? null : {{ $faq->id }})"
                                class="w-full p-4 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-slate-900/60 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-cyan-950/40 border border-cyan-500/30 flex items-center justify-center shrink-0 text-cyan-400">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs sm:text-sm font-bold text-slate-100 block">{{ $faq->question }}</span>
                                    @if($faq->category)
                                        <span class="text-[10px] text-cyan-400 font-medium">{{ $faq->category->name }}</span>
                                    @endif
                                </div>
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
        </div>
    @endif

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
