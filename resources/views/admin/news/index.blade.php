@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">News & Content Management</h1>
            <p class="text-xs text-slate-400 mt-1">Create and manage server announcements and patch updates.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold rounded-xl text-xs shadow-md transition-all flex items-center gap-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Create Article</span>
        </a>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                <tr>
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Category</th>
                    <th class="py-3 px-4">Author</th>
                    <th class="py-3 px-4 text-center">Pinned</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($articles as $a)
                    <tr class="hover:bg-slate-800/30">
                        <td class="py-3 px-4 font-bold text-white">{{ $a->title }}</td>
                        <td class="py-3 px-4 uppercase text-[10px] text-cyan-400 font-semibold">{{ $a->category }}</td>
                        <td class="py-3 px-4 text-slate-300">{{ $a->author_name }}</td>
                        <td class="py-3 px-4 text-center font-bold">{{ $a->is_pinned ? 'YES' : 'NO' }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $a->is_published ? 'bg-emerald-950 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                                {{ $a->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <form method="POST" action="{{ route('admin.news.delete', $a->id) }}" onsubmit="return confirm('Delete this article?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">No news articles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-2">
        {{ $articles->links() }}
    </div>
</div>
@endsection

