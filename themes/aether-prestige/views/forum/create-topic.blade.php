@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <nav class="flex items-center gap-2 text-xs text-[#788394]">
        <a href="{{ route('forum.index') }}" class="hover:text-[#D8BE8A] transition-colors">Community</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-[#D9DEE7] font-semibold">Create New Topic</span>
    </nav>

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Start a New Discussion
            </h1>
            <p class="text-xs text-[#788394] mt-1">Share guides, feedback, or discussions with the community.</p>
        </div>
        <a href="{{ route('forum.index') }}" class="h-9 px-4 rounded-xl bg-[#151B26] border border-white/[0.08] text-xs font-bold text-[#AAB3C1] hover:text-white inline-flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4 text-[#C6A76A]"></i>
            <span>Back to Community</span>
        </a>
    </div>

    <div class="prestige-card p-6 sm:p-8"
         x-data="{ content: {{ json_encode(old('content', '')) }}, preview: false, submitting: false }">
        <form action="{{ route('forum.topic.store') }}" method="POST" enctype="multipart/form-data"
              @submit="if(submitting) { $event.preventDefault(); return; } submitting = true;"
              class="space-y-5">
            @csrf

            <div>
                <label for="board_id" class="block text-xs font-semibold uppercase tracking-wider text-[#D9DEE7] mb-2">Board</label>
                <select id="board_id" name="board_id" required class="w-full px-4 py-2.5 rounded-xl bg-[#101725] border border-white/10 text-sm text-white focus:outline-none focus:border-[#C6A76A]">
                    @foreach($boards as $board)
                        <option value="{{ $board->id }}" @selected(old('board_id', $selectedBoard?->id) == $board->id)>
                            {{ $board->category?->name }} — {{ $board->name }}
                        </option>
                    @endforeach
                </select>
                @error('board_id')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-[#D9DEE7] mb-2">Topic Title</label>
                <input id="title" type="text" name="title" value="{{ old('title') }}" required minlength="4" maxlength="200"
                       placeholder="Enter a clear, descriptive title..."
                       class="w-full px-4 py-2.5 rounded-xl bg-[#101725] border border-white/10 text-sm text-white placeholder-[#788394] focus:outline-none focus:border-[#C6A76A]">
                @error('title')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-[#D9DEE7]">Content</label>
                    <div class="flex items-center gap-3 text-xs">
                        <button type="button" @click="preview = !preview" class="text-[#D8BE8A] hover:underline font-medium cursor-pointer" x-text="preview ? 'Edit' : 'Preview'"></button>
                        <span class="text-[#788394] font-mono" x-text="content.length + ' / 20000'"></span>
                    </div>
                </div>

                <textarea x-show="!preview" id="content" name="content" x-model="content" rows="10" required minlength="10" maxlength="20000"
                          placeholder="Write your discussion content... Use @username to mention members."
                          class="w-full px-4 py-3 rounded-xl bg-[#101725] border border-white/10 text-sm text-white placeholder-[#788394] focus:outline-none focus:border-[#C6A76A]"></textarea>

                <div x-show="preview" style="display: none;" class="min-h-[220px] px-4 py-3 rounded-xl bg-[#101725] border border-white/10 text-sm text-[#D9DEE7] whitespace-pre-line" x-text="content || 'Nothing to preview yet.'"></div>

                @error('content')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="attachments" class="block text-xs font-semibold uppercase tracking-wider text-[#D9DEE7] mb-2">
                    Optional Attachments <span class="text-[#788394] normal-case font-normal">(up to 3 files, max 5MB each: jpg, png, webp, gif, pdf, txt)</span>
                </label>
                <input id="attachments" type="file" name="attachments[]" multiple
                       class="block w-full text-xs text-[#AAB3C1] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-white/10 file:text-xs file:font-semibold file:bg-[#101725] file:text-[#D8BE8A] hover:file:border-[#C6A76A]/50">
                @error('attachments.*')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('forum.index') }}" class="px-4 py-2.5 rounded-xl bg-[#101725] border border-white/10 text-xs font-semibold text-[#AAB3C1] hover:text-white transition-colors">Cancel</a>
                <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] disabled:opacity-50 font-cinzel text-xs font-bold uppercase tracking-wider text-[#101725] cursor-pointer">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span x-text="submitting ? 'Publishing...' : 'Publish Topic'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
