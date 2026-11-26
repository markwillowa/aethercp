@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('forum.index') }}" class="hover:text-cyan-400">Forum</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        <span class="text-slate-200 font-semibold">Create New Topic</span>
    </nav>

    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6 sm:p-8"
         x-data="{ content: {{ json_encode(old('content', '')) }}, preview: false, submitting: false }">
        <h1 class="text-2xl font-bold text-white mb-6">Start a New Discussion</h1>

        <form action="{{ route('forum.topic.store') }}" method="POST" enctype="multipart/form-data"
              @submit="if(submitting) { $event.preventDefault(); return; } submitting = true;"
              class="space-y-5">
            @csrf

            <div>
                <label for="board_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Board</label>
                <select id="board_id" name="board_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-700 text-sm text-slate-200 focus:outline-none focus:border-cyan-500">
                    @foreach($boards as $board)
                        <option value="{{ $board->id }}" @selected(old('board_id', $selectedBoard?->id) == $board->id)>
                            {{ $board->category?->name }} — {{ $board->name }}
                        </option>
                    @endforeach
                </select>
                @error('board_id')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Topic Title</label>
                <input id="title" type="text" name="title" value="{{ old('title') }}" required minlength="4" maxlength="200"
                       placeholder="Enter a clear, descriptive title..."
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-700 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                @error('title')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Content</label>
                    <div class="flex items-center gap-3 text-xs">
                        <button type="button" @click="preview = !preview" class="text-cyan-400 hover:underline font-medium" x-text="preview ? 'Edit' : 'Preview'"></button>
                        <span class="text-slate-500 font-mono" x-text="content.length + ' / 20000'"></span>
                    </div>
                </div>

                <textarea x-show="!preview" id="content" name="content" x-model="content" rows="10" required minlength="10" maxlength="20000"
                          placeholder="Write your discussion content... Use @username to mention members."
                          class="w-full px-4 py-3 rounded-xl bg-slate-950/80 border border-slate-700 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500"></textarea>

                <div x-show="preview" style="display: none;" class="min-h-[220px] px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-200 whitespace-pre-line" x-text="content || 'Nothing to preview yet.'"></div>

                @error('content')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="attachments" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                    Optional Attachments <span class="text-slate-500 normal-case font-normal">(up to 3 files, max 5MB each: jpg, png, webp, gif, pdf, txt)</span>
                </label>
                <input id="attachments" type="file" name="attachments[]" multiple
                       class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-300 hover:file:bg-slate-700">
                @error('attachments.*')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('forum.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sm font-medium text-slate-300 transition-colors">Cancel</a>
                <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 disabled:opacity-50 text-sm font-semibold text-white shadow-lg shadow-cyan-500/20">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span x-text="submitting ? 'Publishing...' : 'Publish Topic'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
