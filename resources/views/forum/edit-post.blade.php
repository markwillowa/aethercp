@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('forum.index') }}" class="hover:text-cyan-400">Forum</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        <a href="{{ route('forum.topic.show', $post->topic->slug) }}" class="hover:text-cyan-400 truncate max-w-xs">{{ $post->topic->title }}</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        <span class="text-slate-200 font-semibold">Edit Post</span>
    </nav>

    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6 sm:p-8"
         x-data="{ content: {{ json_encode(old('content', $post->content)) }}, submitting: false }">
        <h1 class="text-2xl font-bold text-white mb-6">{{ $post->is_first_post ? 'Edit Discussion Topic' : 'Edit Reply' }}</h1>

        <form action="{{ route('forum.post.update', $post->id) }}" method="POST"
              @submit="if(submitting) { $event.preventDefault(); return; } submitting = true;"
              class="space-y-5">
            @csrf
            @method('PUT')

            @if($post->is_first_post)
                <div>
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Topic Title</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $post->topic->title) }}" required minlength="4" maxlength="200"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-700 text-sm text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('title')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Content</label>
                    <span class="text-xs text-slate-500 font-mono" x-text="content.length + ' / 20000'"></span>
                </div>
                <textarea id="content" name="content" x-model="content" rows="10" required minlength="2" maxlength="20000"
                          class="w-full px-4 py-3 rounded-xl bg-slate-950/80 border border-slate-700 text-sm text-slate-200 focus:outline-none focus:border-cyan-500"></textarea>
                @error('content')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('forum.topic.show', $post->topic->slug) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 text-sm font-medium text-slate-300">Cancel</a>
                <button type="submit" :disabled="submitting" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-sm font-semibold text-white">
                    <span x-text="submitting ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
