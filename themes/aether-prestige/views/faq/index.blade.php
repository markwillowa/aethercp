@extends('layouts.app')

@section('title', 'Frequently Asked Questions & Knowledge Base')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <!-- News & Marketplace Style Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Frequently Asked Questions
            </h1>
            <p class="text-xs text-[#788394] mt-1">
                Find clear answers to common questions about server gameplay, account security, downloads, rates, and donation privileges.
            </p>
        </div>
        <span class="h-9 px-3.5 rounded-xl bg-[#151B26] border border-[#C6A76A]/30 font-mono text-xs text-[#D8BE8A] inline-flex items-center shrink-0">
            {{ $categories->count() }} Categories
        </span>
    </div>

    <!-- Search Bar (Matching Cash Shop & Marketplace) -->
    <form action="{{ route('faq.index') }}" method="GET" class="prestige-card p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input type="text" name="q" value="{{ $searchTerm }}" placeholder="Search questions, keywords, commands (e.g. rates, patcher, accounts)..."
               class="flex-1 h-10 px-4 rounded-xl bg-[#101725] border border-white/10 text-white text-xs focus:border-[#C6A76A] focus:outline-none">
        <button type="submit" class="h-10 px-5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider cursor-pointer shrink-0">
            Search
        </button>
        @if($searchTerm)
            <a href="{{ route('faq.index') }}" class="h-10 px-4 rounded-xl bg-[#101725] border border-white/10 text-[#AAB3C1] hover:text-white text-xs font-semibold inline-flex items-center justify-center shrink-0">
                Clear
            </a>
        @endif
    </form>

    <!-- Search Results -->
    @if($searchResults !== null)
        <div class="prestige-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-white/[0.08]">
                <h2 class="font-cinzel text-sm font-bold text-white uppercase tracking-wider">
                    Search Results for "{{ $searchTerm }}" ({{ $searchResults->total() }})
                </h2>
                <a href="{{ route('faq.index') }}" class="text-xs text-[#D8BE8A] hover:underline">Clear Search</a>
            </div>

            @if($searchResults->count() > 0)
                <div class="space-y-3" x-data="{ activeAccordion: null }">
                    @foreach($searchResults as $faq)
                        <div class="rounded-xl bg-[#101725] border border-white/[0.08] overflow-hidden">
                            <button type="button" @click="activeAccordion = (activeAccordion === {{ $faq->id }} ? null : {{ $faq->id }})"
                                    class="w-full p-4 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-white/[0.02] transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#C6A76A]/15 border border-[#C6A76A]/30 flex items-center justify-center shrink-0 text-[#D8BE8A]">
                                        <i data-lucide="help-circle" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs sm:text-sm font-bold text-white block">{{ $faq->question }}</span>
                                        @if($faq->category)
                                            <span class="text-[10px] text-[#C6A76A] font-medium">{{ $faq->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-[#788394] shrink-0 transition-transform duration-200"
                                   :class="activeAccordion === {{ $faq->id }} ? 'rotate-180 text-[#D8BE8A]' : ''"></i>
                            </button>

                            <div x-show="activeAccordion === {{ $faq->id }}" x-collapse class="p-4 pt-0 border-t border-white/[0.06] text-xs text-[#D9DEE7] space-y-3 leading-relaxed">
                                <div class="prose prose-invert max-w-none text-xs text-[#D9DEE7] pt-3">
                                    {!! $faq->answer !!}
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-white/[0.06] text-[11px] text-[#788394]">
                                    <a href="{{ route('faq.show', $faq->slug) }}" class="text-[#D8BE8A] hover:underline flex items-center gap-1 font-semibold">
                                        <span>Direct Link &amp; Details</span>
                                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                    </a>
                                    <span>Updated {{ $faq->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="pt-3">{{ $searchResults->links() }}</div>
            @else
                <div class="py-10 text-center space-y-2">
                    <p class="font-cinzel text-sm font-bold text-white uppercase">No answers matched your query</p>
                    <p class="text-xs text-[#788394]">Try checking your spelling or search for alternative keywords.</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Categories Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-cinzel text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="folder" class="w-4 h-4 text-[#C6A76A]"></i>
                <span>Explore by Category</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($categories as $cat)
                <a href="{{ route('faq.category', $cat->slug) }}"
                   class="prestige-card p-5 flex flex-col justify-between group transition-all">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-[#101725] border border-[#C6A76A]/30 text-[#C6A76A] flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $cat->icon ?: 'folder' }}" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-cinzel text-sm font-bold text-white group-hover:text-[#D8BE8A] transition-colors">
                                {{ $cat->name }}
                            </h3>
                            <p class="text-xs text-[#AAB3C1] line-clamp-2 mt-1 leading-relaxed">
                                {{ $cat->description ?: 'Browse questions and official guides in this category.' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 mt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px] font-mono text-[#788394]">
                        <span>{{ $cat->published_faqs_count }} {{ Str::plural('Question', $cat->published_faqs_count) }}</span>
                        <span class="text-[#D8BE8A] flex items-center gap-1 font-semibold">
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
        <div class="prestige-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-white/[0.08]">
                <h2 class="font-cinzel text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="star" class="w-4 h-4 text-[#C6A76A]"></i>
                    <span>Popular &amp; Frequently Asked Questions</span>
                </h2>
                <span class="text-xs text-[#788394] font-mono">Top Community Topics</span>
            </div>

            <div class="space-y-3" x-data="{ activeAccordion: null }">
                @foreach($featuredFaqs as $faq)
                    <div class="rounded-xl bg-[#101725] border border-white/[0.08] overflow-hidden">
                        <button type="button" @click="activeAccordion = (activeAccordion === {{ $faq->id }} ? null : {{ $faq->id }})"
                                class="w-full p-4 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-white/[0.02] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#C6A76A]/15 border border-[#C6A76A]/30 flex items-center justify-center shrink-0 text-[#D8BE8A]">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs sm:text-sm font-bold text-white block">{{ $faq->question }}</span>
                                    @if($faq->category)
                                        <span class="text-[10px] text-[#C6A76A] font-medium">{{ $faq->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-[#788394] shrink-0 transition-transform duration-200"
                               :class="activeAccordion === {{ $faq->id }} ? 'rotate-180 text-[#D8BE8A]' : ''"></i>
                        </button>

                        <div x-show="activeAccordion === {{ $faq->id }}" x-collapse class="p-4 pt-0 border-t border-white/[0.06] text-xs text-[#D9DEE7] space-y-3 leading-relaxed">
                            <div class="prose prose-invert max-w-none text-xs text-[#D9DEE7] pt-3">
                                {!! $faq->answer !!}
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-white/[0.06] text-[11px] text-[#788394]">
                                <a href="{{ route('faq.show', $faq->slug) }}" class="text-[#D8BE8A] hover:underline flex items-center gap-1 font-semibold">
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

    <!-- Still Need Help CTA -->
    <div class="prestige-card p-6 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-[#101725] border border-[#C6A76A]/30 text-[#C6A76A] flex items-center justify-center shrink-0">
                <i data-lucide="life-buoy" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="font-cinzel text-base font-bold text-white uppercase tracking-wide">Still need help?</h3>
                <p class="text-xs text-[#AAB3C1] mt-0.5">
                    If you cannot find the answer you are looking for, our server support team and game masters are ready to assist.
                </p>
            </div>
        </div>

        @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support'))
            <a href="{{ route('support.index') }}" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider shrink-0 inline-flex items-center gap-2">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>Contact Support</span>
            </a>
        @else
            <a href="{{ \App\Models\SiteSetting::get('social_discord', '#') }}" target="_blank" rel="noopener" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider shrink-0 inline-flex items-center gap-2">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span>Join Discord Support</span>
            </a>
        @endif
    </div>
</div>
@endsection
