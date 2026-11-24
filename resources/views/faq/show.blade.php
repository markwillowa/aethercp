@extends('layouts.app')

@section('title', ($faq->seo_title ?: $faq->question) . ' - FAQ')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto py-2">

    <!-- Breadcrumb & Back Link -->
    <div class="flex items-center justify-between text-xs text-slate-400">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-cyan-400 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('faq.index') }}" class="hover:text-cyan-400 transition-colors">FAQ</a>
            @if($faq->category)
                <span>/</span>
                <a href="{{ route('faq.category', $faq->category->slug) }}" class="hover:text-cyan-400 transition-colors">{{ $faq->category->name }}</a>
            @endif
            <span>/</span>
            <span class="text-cyan-400 font-medium truncate max-w-xs">{{ $faq->question }}</span>
        </div>
        <a href="{{ route('faq.index') }}" class="text-cyan-400 hover:text-cyan-300 flex items-center gap-1 shrink-0 font-semibold">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Question Article Card -->
    <article class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 sm:p-8 space-y-6 shadow-xl">
        <!-- Header -->
        <div class="space-y-3 pb-5 border-b border-slate-800">
            <div class="flex items-center gap-2 flex-wrap">
                @if($faq->category)
                    <a href="{{ route('faq.category', $faq->category->slug) }}" 
                       class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wider bg-cyan-500/10 border border-cyan-500/30 text-cyan-400">
                        {{ $faq->category->name }}
                    </a>
                @endif
                <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                    <i data-lucide="clock" class="w-3 h-3"></i>
                    <span>Updated {{ $faq->updated_at->format('M d, Y') }}</span>
                </span>
                <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                    <i data-lucide="eye" class="w-3 h-3"></i>
                    <span>{{ number_format($faq->views_count) }} views</span>
                </span>
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">
                {{ $faq->question }}
            </h1>

            @if($faq->short_description)
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed italic">
                    {{ $faq->short_description }}
                </p>
            @endif
        </div>

        <!-- Full Answer Body -->
        <div class="prose prose-invert max-w-none text-xs sm:text-sm text-slate-300 leading-relaxed space-y-3">
            {!! $faq->answer !!}
        </div>

        <!-- Interactive Feedback Widget -->
        <div class="p-5 rounded-xl bg-slate-950 border border-slate-800 space-y-3"
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
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Was this answer helpful?</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5" x-text="message || 'Help us improve our documentation by rating this guide.'"></p>
                </div>

                <div class="flex items-center gap-2" x-show="!voted">
                    <button type="button" @click="submitVote('helpful')" 
                            class="px-4 py-1.5 rounded-lg text-xs font-bold bg-emerald-950/40 border border-emerald-500/40 text-emerald-400 hover:bg-emerald-900/40 hover:text-emerald-300 transition-all cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="thumbs-up" class="w-3.5 h-3.5"></i>
                        <span>Yes</span>
                    </button>
                    <button type="button" @click="submitVote('not_helpful')" 
                            class="px-4 py-1.5 rounded-lg text-xs font-bold bg-rose-950/40 border border-rose-500/40 text-rose-400 hover:bg-rose-900/40 hover:text-rose-300 transition-all cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="thumbs-down" class="w-3.5 h-3.5"></i>
                        <span>No</span>
                    </button>
                </div>

                <div class="text-[11px] font-mono text-cyan-400 flex items-center gap-1.5" x-show="voted">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                    <span><strong x-text="percent + '%'">{{ $faq->helpfulPercentage() }}%</strong> of players found this helpful</span>
                </div>
            </div>
        </div>

        <!-- Related Questions -->
        @if($faq->relatedQuestions->count() > 0)
            <div class="pt-5 border-t border-slate-800 space-y-3">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="link" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Related Questions</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($faq->relatedQuestions as $related)
                        <a href="{{ route('faq.show', $related->slug) }}" 
                           class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-cyan-500/50 hover:bg-slate-900/60 transition-all flex items-start gap-2.5 text-xs text-slate-300 group">
                            <i data-lucide="help-circle" class="w-4 h-4 text-cyan-400 shrink-0 mt-0.5 group-hover:scale-110 transition-transform"></i>
                            <span class="group-hover:text-cyan-300 transition-colors font-medium">{{ $related->question }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>

    <!-- Still Need Help Support CTA -->
    <div class="rounded-2xl p-6 sm:p-8 border border-slate-800 bg-slate-900/90 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center shrink-0 shadow-inner">
                <i data-lucide="life-buoy" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white uppercase tracking-wide">Still have questions?</h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Our support staff and game masters are available to assist with player account and technical inquiries.
                </p>
            </div>
        </div>

        @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support'))
            <a href="{{ route('support.index') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold py-2.5 px-6 rounded-xl text-xs uppercase tracking-wider shrink-0 flex items-center gap-2 shadow-lg transition-all">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>Open Support Ticket</span>
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
