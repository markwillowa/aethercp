@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('forum.index') }}" class="hover:text-cyan-400">Forum</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        @if($topic->board)
            <a href="{{ route('forum.board', $topic->board->slug) }}" class="hover:text-cyan-400">{{ $topic->board->name }}</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        @endif
        <span class="text-slate-200 font-semibold truncate max-w-xs sm:max-w-md">{{ $topic->title }}</span>
    </nav>

    <!-- Topic Header -->
    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-forum.topic-status-badge :topic="$topic" />

            <div class="flex flex-wrap items-center gap-2">
                @auth
                    <form action="{{ route('forum.topic.subscribe', $topic->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors {{ $isSubscribed ? 'bg-cyan-500/20 border-cyan-500/40 text-cyan-300' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:border-cyan-500/40' }}">
                            <i data-lucide="{{ $isSubscribed ? 'bell-ring' : 'bell' }}" class="w-3.5 h-3.5"></i>
                            <span>{{ $isSubscribed ? 'Subscribed' : 'Subscribe' }}</span>
                        </button>
                    </form>

                    <button type="button" onclick="window.openForumReportModal({{ $topic->id }}, null)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-950/60 border border-slate-800 text-slate-400 hover:text-amber-300 transition-colors">
                        <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                        <span>Report</span>
                    </button>

                    @if(auth()->user()->isStaff())
                        <form action="{{ route('forum.topic.pin', $topic->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-amber-500/10 border border-amber-500/30 text-amber-300 hover:bg-amber-500/20">
                                {{ $topic->is_pinned ? 'Unpin' : 'Pin' }}
                            </button>
                        </form>
                        <form action="{{ route('forum.topic.lock', $topic->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-300 hover:bg-slate-700">
                                {{ $topic->is_locked ? 'Unlock' : 'Lock' }}
                            </button>
                        </form>
                        @if(!$topic->is_approved_guide)
                            <form action="{{ route('forum.topic.approve-guide', $topic->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 hover:bg-cyan-500/20">
                                    Approve Guide
                                </button>
                            </form>
                        @endif
                    @endif
                @endauth
            </div>
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">{{ $topic->title }}</h1>

        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400">
            <span>Started by <strong class="text-slate-200">{{ $identities[$topic->user_id]['character']['name'] ?? $identities[$topic->user_id]['username'] ?? $topic->author?->username }}</strong></span>
            <span>&bull;</span>
            <span>{{ $topic->created_at?->format('M j, Y · g:i A') }}</span>
            <span>&bull;</span>
            <span class="inline-flex items-center gap-1"><i data-lucide="message-circle" class="w-3.5 h-3.5 text-cyan-400"></i> {{ number_format($topic->replies_count) }} replies</span>
            <span class="inline-flex items-center gap-1"><i data-lucide="eye" class="w-3.5 h-3.5 text-blue-400"></i> {{ number_format($topic->views_count) }} views</span>
        </div>
    </div>

    <!-- Posts -->
    <div class="space-y-4">
        @foreach($posts as $post)
            <x-forum.post-card
                :post="$post"
                :topic="$topic"
                :identity="$identities[$post->user_id] ?? null"
                :reacted="in_array($post->id, $reactedPostIds, true)" />
        @endforeach
    </div>

    @if($posts->hasPages())
        <div>{{ $posts->links() }}</div>
    @endif

    <!-- Reply Editor -->
    @auth
        @if(!$topic->is_locked || auth()->user()->isStaff())
            <div id="reply-editor"
                 class="rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6"
                 x-data="{ content: {{ json_encode(old('content', '')) }}, preview: false, submitting: false }">
                <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                    <i data-lucide="reply" class="w-4 h-4 text-cyan-400"></i>
                    <span>Post a Reply</span>
                </h2>

                <form action="{{ route('forum.topic.reply', $topic->slug) }}" method="POST" enctype="multipart/form-data"
                      @submit="if(submitting || content.trim().length < 2) { $event.preventDefault(); return; } submitting = true;"
                      class="space-y-4">
                    @csrf

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400">Tip: Use <code class="text-cyan-400">@username</code> to mention members or click Quote on any post.</span>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="preview = !preview" class="text-cyan-400 hover:underline font-medium" x-text="preview ? 'Edit' : 'Preview'"></button>
                            <span class="text-slate-500 font-mono" x-text="content.length + ' / 20000'"></span>
                        </div>
                    </div>

                    <textarea x-show="!preview" id="reply-textarea" name="content" x-model="content" rows="6" required minlength="2" maxlength="20000"
                              placeholder="Write your reply..."
                              class="w-full px-4 py-3 rounded-xl bg-slate-950/80 border border-slate-700 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500"></textarea>

                    <div x-show="preview" style="display: none;" class="min-h-[150px] px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-200 whitespace-pre-line" x-text="content || 'Nothing to preview.'"></div>

                    @error('content')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <input type="file" name="attachments[]" multiple
                               class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-300 hover:file:bg-slate-700">

                        <button type="submit" :disabled="submitting" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 disabled:opacity-50 text-sm font-semibold text-white shadow-lg shadow-cyan-500/20">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span x-text="submitting ? 'Posting...' : 'Post Reply'"></span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4 text-center text-sm text-slate-400">
                <i data-lucide="lock" class="w-4 h-4 inline mr-1"></i> This discussion is locked and no longer accepts replies.
            </div>
        @endif
    @else
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-6 text-center space-y-2">
            <p class="text-sm text-slate-300">Sign in to your Aether account to join this discussion.</p>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-cyan-500 text-slate-950 font-semibold text-sm">Sign In</a>
        </div>
    @endauth
</div>

<!-- Report Modal -->
@auth
<div id="forum-report-modal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-white">Report Content</h3>
            <button type="button" onclick="window.closeForumReportModal()" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('forum.report') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="topic_id" id="report-topic-id">
            <input type="hidden" name="post_id" id="report-post-id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reason</label>
                <select name="reason" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-sm text-slate-200">
                    <option value="Spam or Advertising">Spam or Advertising</option>
                    <option value="Harassment or Toxicity">Harassment or Toxicity</option>
                    <option value="Off-topic or Misplaced">Off-topic or Misplaced</option>
                    <option value="Rule Violation / Exploits">Rule Violation / Exploits</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Additional Details (Optional)</label>
                <textarea name="details" rows="3" maxlength="1000" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-sm text-slate-200"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="window.closeForumReportModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-xs font-semibold text-slate-300">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold">Submit Report</button>
            </div>
        </form>
    </div>
</div>
@endauth

<script>
    window.quoteForumPost = function (author, text) {
        const textarea = document.getElementById('reply-textarea');
        if (!textarea) return;
        const quote = `[quote="${author}"]\n${text}\n[/quote]\n\n`;
        textarea.value = (textarea.value ? textarea.value + '\n' : '') + quote;
        textarea.dispatchEvent(new Event('input'));
        textarea.focus();
        document.getElementById('reply-editor')?.scrollIntoView({ behavior: 'smooth' });
    };

    window.openForumReportModal = function (topicId, postId) {
        const modal = document.getElementById('forum-report-modal');
        if (!modal) return;
        document.getElementById('report-topic-id').value = topicId || '';
        document.getElementById('report-post-id').value = postId || '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeForumReportModal = function () {
        const modal = document.getElementById('forum-report-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
</script>
@endsection
