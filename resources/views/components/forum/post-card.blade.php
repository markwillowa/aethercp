@props(['post', 'topic', 'identity' => null, 'reacted' => false])
@php
    $canEdit = auth()->check() && (auth()->id() === $post->user_id || auth()->user()->isStaff());
    $canAccept = auth()->check() && !$post->is_first_post && (auth()->id() === $topic->user_id || auth()->user()->isStaff());
    $isStaff = auth()->check() && auth()->user()->isStaff();
    $displayName = $identity['character']['name'] ?? $identity['username'] ?? $post->author?->username ?? 'Member';
@endphp
<article id="post-{{ $post->id }}"
         class="rounded-2xl border {{ $post->is_accepted_answer ? 'border-emerald-500/50 bg-emerald-950/15' : 'border-slate-800/80 bg-slate-900/65' }} backdrop-blur-md overflow-hidden transition-all">
    @if($post->is_accepted_answer)
        <div class="px-5 py-2 bg-emerald-500/15 border-b border-emerald-500/30 flex items-center gap-2 text-xs font-semibold text-emerald-300">
            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            <span>Accepted Answer</span>
        </div>
    @endif

    <div class="p-5 sm:p-6 flex flex-col lg:flex-row gap-6">
        <!-- Author Identity Sidebar -->
        <aside class="lg:w-60 shrink-0 lg:border-r lg:border-slate-800/80 lg:pr-5">
            <x-forum.user-identity :identity="$identity" :show-points="true" />
            @if(!empty($post->author?->forumProfile?->forum_title))
                <div class="mt-2 text-xs text-slate-400 italic">{{ $post->author->forumProfile->forum_title }}</div>
            @endif
        </aside>

        <!-- Post Body & Actions -->
        <div class="flex-1 min-w-0 flex flex-col justify-between">
            <div>
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-4 border-b border-slate-800/70 text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <span>Posted {{ $post->created_at?->format('M j, Y · g:i A') }}</span>
                        @if($post->edited_at)
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[11px]" title="Edited {{ $post->edited_at->diffForHumans() }}">
                                Edited {{ $post->edited_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                    <a href="#post-{{ $post->id }}" class="text-slate-500 hover:text-cyan-400 font-mono">#{{ $post->id }}</a>
                </div>

                <div class="prose prose-invert prose-sm max-w-none text-slate-200 leading-relaxed break-words">
                    {!! \App\Services\Forum\ForumService::formatPostHtml($post->content) !!}
                </div>

                @if($post->attachments && $post->attachments->isNotEmpty())
                    <div class="mt-5 pt-4 border-t border-slate-800/70">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Attachments</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->attachments as $att)
                                <a href="{{ route('forum.attachment.download', $att->id) }}"
                                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-950/70 border border-slate-800 hover:border-cyan-500/40 text-xs text-slate-300 hover:text-cyan-300 transition-colors">
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5 text-cyan-400"></i>
                                    <span class="truncate max-w-[180px]">{{ $att->original_name }}</span>
                                    <span class="text-slate-500 font-mono">({{ number_format(max(1, $att->file_size / 1024), 0) }} KB)</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Bar -->
            <div class="mt-6 pt-4 border-t border-slate-800/70 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    @auth
                        @if(auth()->id() !== $post->user_id)
                            <form action="{{ route('forum.post.react', $post->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all {{ $reacted ? 'bg-cyan-500/20 border-cyan-500/50 text-cyan-300' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:border-cyan-500/40 hover:text-cyan-300' }}">
                                    <i data-lucide="thumbs-up" class="w-3.5 h-3.5"></i>
                                    <span>{{ $reacted ? 'Liked' : 'Like' }}</span>
                                    <span class="font-mono px-1.5 py-0.2 rounded bg-slate-900/80 text-[11px]">{{ $post->likes_count }}</span>
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-950/40 border border-slate-800/70 text-slate-400">
                                <i data-lucide="thumbs-up" class="w-3.5 h-3.5"></i>
                                <span class="font-mono">{{ $post->likes_count }}</span>
                            </span>
                        @endif

                        @if(!$topic->is_locked || $isStaff)
                            <button type="button"
                                    onclick="window.quoteForumPost('{{ addslashes($displayName) }}', {{ json_encode($post->content) }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-950/60 border border-slate-800 text-slate-300 hover:border-slate-600 hover:text-white transition-colors">
                                <i data-lucide="quote" class="w-3.5 h-3.5"></i>
                                <span>Quote</span>
                            </button>
                        @endif

                        @if($canAccept && !$post->is_accepted_answer)
                            <form action="{{ route('forum.topic.accept', [$topic->id, $post->id]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 hover:bg-emerald-500/20 transition-colors">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    <span>Accept Answer</span>
                                </button>
                            </form>
                        @endif

                        @if($isStaff && auth()->id() !== $post->user_id)
                            <form action="{{ route('forum.post.recognize', $post->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-500/10 border border-amber-500/30 text-amber-300 hover:bg-amber-500/20 transition-colors" title="Award Moderator Recognition">
                                    <i data-lucide="award" class="w-3.5 h-3.5"></i>
                                    <span>Recognize</span>
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-950/40 border border-slate-800/70 text-slate-400">
                            <i data-lucide="thumbs-up" class="w-3.5 h-3.5"></i>
                            <span class="font-mono">{{ $post->likes_count }}</span>
                        </span>
                    @endauth
                </div>

                <div class="flex items-center gap-2">
                    @if($canEdit)
                        <a href="{{ route('forum.post.edit', $post->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs text-slate-300 hover:text-white bg-slate-950/60 border border-slate-800 hover:border-slate-600 transition-colors">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('forum.post.delete', $post->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this post?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs text-red-300 hover:text-red-200 bg-red-950/30 border border-red-500/20 hover:border-red-500/40 transition-colors">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    @endif

                    @auth
                        <button type="button"
                                onclick="window.openForumReportModal({{ $topic->id }}, {{ $post->id }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs text-slate-400 hover:text-amber-300 bg-slate-950/40 border border-slate-800/70 transition-colors">
                            <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                            <span>Report</span>
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</article>
