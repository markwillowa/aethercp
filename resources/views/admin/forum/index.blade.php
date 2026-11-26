@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Forum &amp; Boards Management</h1>
            <p class="text-xs text-slate-400 mt-1">Manage forum categories, discussion boards, and community moderation reports.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.forum.reputation.settings') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700">Reputation Settings</a>
            <a href="{{ route('admin.forum.reputation.history') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700">Reputation Ledger</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Create Category -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Create Category</h2>
            <form action="{{ route('admin.forum.categories.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="name" required placeholder="Category Name" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                <input type="text" name="description" placeholder="Short description" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                <div class="flex gap-3">
                    <input type="text" name="icon" value="message-square" placeholder="Lucide icon" class="w-1/2 px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                    <input type="number" name="sort_order" value="0" placeholder="Order" class="w-1/2 px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 text-slate-950 text-xs font-bold">Add Category</button>
            </form>
        </div>

        <!-- Create Board -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Create Board</h2>
            <form action="{{ route('admin.forum.boards.store') }}" method="POST" class="space-y-3">
                @csrf
                <select name="category_id" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" required placeholder="Board Name" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                <input type="text" name="description" placeholder="Board description" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200">
                <label class="inline-flex items-center gap-2 text-xs text-slate-300">
                    <input type="checkbox" name="staff_only" value="1" class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                    <span>Staff-only topic creation (e.g., Announcements)</span>
                </label>
                <div>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 text-slate-950 text-xs font-bold">Add Board</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories & Boards List -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Active Categories &amp; Boards</h2>
        @foreach($categories as $category)
            <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 space-y-2">
                <div class="font-bold text-sm text-cyan-300">{{ $category->name }}</div>
                <div class="space-y-2">
                    @foreach($category->boards as $board)
                        <div class="flex items-center justify-between text-xs bg-slate-900/70 px-3 py-2 rounded-lg border border-slate-800/80">
                            <div>
                                <span class="font-semibold text-white">{{ $board->name }}</span>
                                <span class="text-slate-500 ml-2">{{ $board->topics_count }} topics &bull; {{ $board->posts_count }} posts</span>
                            </div>
                            <form action="{{ route('admin.forum.boards.toggle-lock', $board->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded bg-slate-800 text-slate-300 hover:text-white">
                                    {{ $board->is_locked ? 'Unlock' : 'Lock' }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Moderation Reports -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Content Reports</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase border-b border-slate-800">
                    <tr>
                        <th class="py-2 pr-4">Reporter</th>
                        <th class="py-2 pr-4">Reason</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">Date</th>
                        <th class="py-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($reports as $report)
                        <tr>
                            <td class="py-3 pr-4 text-slate-200">{{ $report->reporter?->username ?? 'Unknown' }}</td>
                            <td class="py-3 pr-4 text-slate-300">
                                <div class="font-semibold">{{ $report->reason }}</div>
                                @if($report->details)
                                    <div class="text-slate-500">{{ $report->details }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-4 uppercase font-bold {{ $report->status === 'pending' ? 'text-amber-400' : 'text-emerald-400' }}">{{ $report->status }}</td>
                            <td class="py-3 pr-4 text-slate-500">{{ $report->created_at?->diffForHumans() }}</td>
                            <td class="py-3 text-right space-x-1">
                                @if($report->status === 'pending')
                                    <form action="{{ route('admin.forum.reports.resolve', $report->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="reviewed">
                                        <button type="submit" class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-300">Mark Reviewed</button>
                                    </form>
                                    <form action="{{ route('admin.forum.reports.resolve', $report->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="dismissed">
                                        <button type="submit" class="px-2.5 py-1 rounded bg-slate-800 text-slate-400">Dismiss</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">No reports submitted.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->links() }}
    </div>
</div>
@endsection
