@extends('layouts.app')

@section('title', ($faq->seo_title ?: $faq->question) . ' - FAQ')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto py-2">

    <!-- Breadcrumb & Back Link -->
    <div class="flex items-center justify-between text-xs text-[#A898B5]">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('faq.index') }}" class="hover:text-white transition-colors">FAQ</a>
            @if($faq->category)
                <span>/</span>
                <a href="{{ route('faq.category', $faq->category->slug) }}" class="hover:text-white transition-colors">{{ $faq->category->name }}</a>
            @endif
            <span>/</span>
            <span class="text-[#FFE894] font-medium truncate max-w-xs">{{ $faq->question }}</span>
        </div>
        <a href="{{ route('faq.index') }}" class="text-[#D4AF37] hover:text-[#FCE69B] flex items-center gap-1 shrink-0">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Question Article Card -->
    <article class="chronicles-panel p-6 sm:p-8 space-y-6 shadow-2xl">
        <!-- Header -->
        <div class="space-y-3 pb-5 border-b border-[#352643]">
            <div class="flex items-center gap-2 flex-wrap">
                @if($faq->category)
                    <a href="{{ route('faq.category', $faq->category->slug) }}" 
                       class="px-2.5 py-0.5 rounded-full text-[10px] font-cinzel font-bold tracking-wider bg-[#D4AF37]/15 border border-[#D4AF37]/40 text-[#FCE69B]">
                        {{ $faq->category->name }}
                    </a>
                @endif
                <span class="text-[11px] text-[#8C7B99] font-mono flex items-center gap-1">
                    <i data-lucide="clock" class="w-3 h-3"></i>
                    <span>Updated {{ $faq->updated_at->format('M d, Y') }}</span>
                </span>
                <span class="text-[11px] text-[#8C7B99] font-mono flex items-center gap-1">
                    <i data-lucide="eye" class="w-3 h-3"></i>
                    <span>{{ number_format($faq->views_count) }} views</span>
                </span>
            </div>

            <h1 class="text-xl sm:text-2xl font-black font-cinzel text-[#FFE4A0] leading-snug">
                {{ $faq->question }}
            </h1>

            @if($faq->short_description)
                <p class="text-xs sm:text-sm text-[#C8BDAA] leading-relaxed italic">
                    {{ $faq->short_description }}
                </p>
            @endif
        </div>

        <!-- Full Answer Body -->
        <div class="prose prose-invert max-w-none text-xs sm:text-sm text-[#E2D9EB] leading-relaxed space-y-3">
            {!! $faq->answer !!}
        </div>

        <!-- Interactive Feedback Widget -->
        <div class="p-5 rounded-xl bg-[#140E1B] border border-[#3E2F4D] space-y-3"
             x-data="{
                voted: {{ $hasVoted ? 'true' : 'false' }},
                message: '{{ $hasVoted ? 'You have already voted on this answer.' : '' }}',
                percent: {{ $faq->helpfulPercentage() }},
                submitVote(type) {
                    if (this.voted) return;
                    fetch('{{ route('faq.feedback', $faq->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ type: type })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.voted = true;
                        this.message = data.message;
                        if (data.helpful_percentage !== undefined) {
                            this.percent = data.helpful_percentage;
                        }
                    })
                    .catch(() => {
                        this.message = 'An error occurred while saving your feedback.';
                    });
                }
             }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h4 class="text-xs font-bold text-white font-cinzel uppercase tracking-wider">Was this answer helpful?</h4>
                    <p class="text-[11px] text-[#A898B5] mt-0.5" x-text="message || 'Help us improve our documentation by rating this guide.'"></p>
                </div>

                <div class="flex items-center gap-2" x-show="!voted">
                    <button type="button" @click="submitVote('helpful')" 
                            class="px-4 py-1.5 rounded-lg text-xs font-bold bg-[#1C2C1D] border border-emerald-500/50 text-emerald-300 hover:bg-emerald-900/40 hover:text-white transition-all cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="thumbs-up" class="w-3.5 h-3.5"></i>
                        <span>Yes</span>
                    </button>
                    <button type="button" @click="submitVote('not_helpful')" 
                            class="px-4 py-1.5 rounded-lg text-xs font-bold bg-[#2C1C1D] border border-rose-500/50 text-rose-300 hover:bg-rose-900/40 hover:text-white transition-all cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="thumbs-down" class="w-3.5 h-3.5"></i>
                        <span>No</span>
                    </button>
                </div>

                <div class="text-[11px] font-mono text-[#D4AF37] flex items-center gap-1.5" x-show="voted">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                    <span><strong x-text="percent + '%'">{{ $faq->helpfulPercentage() }}%</strong> of players found this helpful</span>
                </div>
            </div>
        </div>

        <!-- Related Questions -->
        @if($faq->relatedQuestions->count() > 0)
            <div class="pt-5 border-t border-[#352643] space-y-3">
                <h3 class="text-xs font-black font-cinzel text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="link" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Related Questions</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($faq->relatedQuestions as $related)
                        <a href="{{ route('faq.show', $related->slug) }}" 
                           class="p-3 rounded-xl bg-[#140E1B] border border-[#3E2F4D] hover:border-[#D4AF37]/60 hover:bg-[#1E1528] transition-all flex items-start gap-2.5 text-xs text-[#E2D9EB] group">
                            <i data-lucide="help-circle" class="w-4 h-4 text-[#D4AF37] shrink-0 mt-0.5 group-hover:scale-110 transition-transform"></i>
                            <span class="group-hover:text-[#FFE894] transition-colors font-medium">{{ $related->question }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>

    <!-- Still Need Help Support CTA -->
    <div class="rounded-2xl p-6 sm:p-8 border border-[#5A456B] flex flex-col sm:flex-row items-center justify-between gap-6"
         style="background: linear-gradient(135deg, #1C1226 0%, #120A1A 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-[#D4AF37]/20 border border-[#D4AF37]/50 text-[#FFD700] flex items-center justify-center shrink-0 shadow-inner">
                <i data-lucide="life-buoy" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-base font-bold font-cinzel text-white uppercase tracking-wide">Still have questions?</h3>
                <p class="text-xs text-[#C8BDAA] mt-0.5">
                    Our support staff and game masters are available to assist with player account and technical inquiries.
                </p>
            </div>
        </div>

        @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support'))
            <a href="{{ route('support.index') }}" class="chronicles-btn-gold py-2.5 px-6 font-cinzel font-bold text-xs uppercase tracking-wider shrink-0 flex items-center gap-2 shadow-lg">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>Open Support Ticket</span>
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
