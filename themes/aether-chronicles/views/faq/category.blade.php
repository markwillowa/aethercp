@extends('layouts.app')

@section('title', $category->name . ' - Knowledge Base')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto py-2">

    <!-- Breadcrumb & Back Link -->
    <div class="flex items-center justify-between text-xs text-[#A898B5]">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('faq.index') }}" class="hover:text-white transition-colors">FAQ</a>
            <span>/</span>
            <span class="text-[#FFE894] font-medium">{{ $category->name }}</span>
        </div>
        <a href="{{ route('faq.index') }}" class="text-[#D4AF37] hover:text-[#FCE69B] flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>All Categories</span>
        </a>
    </div>

    <!-- Category Header Card -->
    <div class="p-6 rounded-2xl border border-[#5A456B] flex items-start gap-4"
         style="background: linear-gradient(180deg, #1F152B 0%, #140D1D 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.6);">
        <div class="w-12 h-12 rounded-xl bg-[#291D38] border border-[#5A456B] text-[#FFD700] flex items-center justify-center shrink-0 shadow-inner">
            <i data-lucide="{{ $category->icon ?: 'folder' }}" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h1 class="text-xl font-black font-cinzel text-white uppercase tracking-wide">
                {{ $category->name }}
            </h1>
            <p class="text-xs text-[#C8BDAA] mt-1 leading-relaxed">
                {{ $category->description ?: 'Browse published questions and answers in this category.' }}
            </p>
            <span class="inline-block mt-2 text-[10px] font-mono text-[#D4AF37] bg-[#2B1E38] px-2.5 py-0.5 rounded border border-[#D4AF37]/30">
                {{ $faqs->total() }} {{ Str::plural('Question', $faqs->total()) }} Available
            </span>
        </div>
    </div>

    <!-- Questions Accordion -->
    <div class="chronicles-panel p-5 space-y-4">
        @if($faqs->count() > 0)
            <div class="space-y-3" x-data="{ activeAccordion: null }">
                @foreach($faqs as $faq)
                    <div class="rounded-xl bg-[#140E1B] border border-[#3E2F4D] overflow-hidden transition-all">
                        <button type="button" @click="activeAccordion = (activeAccordion === {{ $faq->id }} ? null : {{ $faq->id }})"
                                class="w-full p-4 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-[#1E1528] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#251A33] border border-[#5A456B] flex items-center justify-center shrink-0 text-[#FFE894]">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-white block">{{ $faq->question }}</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-[#C9A96E] shrink-0 transition-transform duration-200"
                               :class="activeAccordion === {{ $faq->id }} ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="activeAccordion === {{ $faq->id }}" x-collapse class="p-4 pt-0 border-t border-[#251A33] bg-[#0E0814] text-xs text-[#D2C7DD] space-y-3 leading-relaxed">
                            <div class="prose prose-invert max-w-none text-xs text-[#D2C7DD] pt-3">
                                {!! $faq->answer !!}
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-[#1C1425] text-[11px] text-[#8C7B99]">
                                <a href="{{ route('faq.show', $faq->slug) }}" class="text-[#D4AF37] hover:underline flex items-center gap-1 font-semibold">
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
                <i data-lucide="inbox" class="w-10 h-10 text-[#6B537F] mx-auto"></i>
                <p class="text-sm font-bold text-white">No questions published yet</p>
                <p class="text-xs text-[#A898B5]">Check back soon or submit a support ticket if you need guidance.</p>
            </div>
        @endif
    </div>

    <!-- Still Need Help Support CTA -->
    <div class="rounded-2xl p-6 sm:p-8 border border-[#5A456B] flex flex-col sm:flex-row items-center justify-between gap-6"
         style="background: linear-gradient(135deg, #1C1226 0%, #120A1A 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-[#D4AF37]/20 border border-[#D4AF37]/50 text-[#FFD700] flex items-center justify-center shrink-0 shadow-inner">
                <i data-lucide="life-buoy" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-base font-bold font-cinzel text-white uppercase tracking-wide">Still need help?</h3>
                <p class="text-xs text-[#C8BDAA] mt-0.5">
                    If you cannot find the answer you are looking for, our server support team and game masters are ready to assist.
                </p>
            </div>
        </div>

        @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support'))
            <a href="{{ route('support.index') }}" class="chronicles-btn-gold py-2.5 px-6 font-cinzel font-bold text-xs uppercase tracking-wider shrink-0 flex items-center gap-2 shadow-lg">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>Contact Support</span>
            </a>
        @else
            <a href="{{ \App\Models\SiteSetting::get('social_discord', '#') }}" target="_blank" rel="noopener" class="chronicles-btn-gold py-2.5 px-6 font-cinzel font-bold text-xs uppercase tracking-wider shrink-0 flex items-center gap-2 shadow-lg">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span>Join Discord Support</span>
            </a>
        @endif
    </div>

</div>
@endsection
