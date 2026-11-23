@extends('layouts.admin')

@section('admin_content')
<div class="max-w-3xl space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Create News Article</h1>
        <p class="text-xs text-slate-400 mt-1">Publish a server announcement, maintenance notice, or patch note.</p>
    </div>

    <form method="POST" action="{{ route('admin.news.store') }}" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Title</label>
            <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Episode 19 Patch Notes"
                class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Category</label>
                <select name="category" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    <option value="news">News</option>
                    <option value="announcement">Announcement</option>
                    <option value="event">Event</option>
                    <option value="update">Update</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <div class="flex items-center gap-4 pt-5">
                <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-300">
                    <input type="checkbox" name="is_pinned" value="1" class="rounded bg-slate-950 border-slate-800 text-cyan-500">
                    <span>Pin to top</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-300">
                    <input type="checkbox" name="is_published" value="1" checked class="rounded bg-slate-950 border-slate-800 text-cyan-500">
                    <span>Publish immediately</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Short Summary</label>
            <textarea name="summary" rows="2" placeholder="Brief 1-2 sentence preview..."
                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Content (Markdown Supported)</label>
            <textarea name="content" rows="10" required placeholder="Write full article content here..."
                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 font-mono"></textarea>
        </div>

        <button type="submit" class="py-2.5 px-6 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-md transition-colors">
            Publish Article
        </button>
    </form>
</div>
@endsection

