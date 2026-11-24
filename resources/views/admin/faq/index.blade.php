@extends('layouts.admin')

@section('title', 'FAQ & Knowledge Base Management')

@section('admin_content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="help-circle" class="w-6 h-6 text-cyan-400"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide uppercase">FAQ &amp; Knowledge Base</h1>
                <p class="text-xs text-slate-400">Publish categorized guides, track player feedback, and manage documentation.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.faq.categories') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="folder" class="w-4 h-4 text-cyan-400"></i>
                <span>Categories ({{ $stats['categoriesCount'] }})</span>
            </a>
            <a href="{{ route('admin.faq.create') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold py-2 px-4 rounded-xl text-xs uppercase tracking-wider flex items-center gap-1.5 shadow-md transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Create FAQ</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-emerald-400"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistics Metrics Row -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total FAQs</span>
            <span class="text-xl font-mono font-bold text-white">{{ number_format($stats['totalFaqs']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-emerald-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">Published</span>
            <span class="text-xl font-mono font-bold text-emerald-300">{{ number_format($stats['published']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-amber-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 block mb-1">Drafts</span>
            <span class="text-xl font-mono font-bold text-amber-300">{{ number_format($stats['draft']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-cyan-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 block mb-1">Categories</span>
            <span class="text-xl font-mono font-bold text-cyan-300">{{ number_format($stats['categoriesCount']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-purple-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400 block mb-1">Total Views</span>
            <span class="text-xl font-mono font-bold text-purple-300">{{ number_format($stats['totalViews']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-cyan-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 block mb-1">Helpful Rating</span>
            <span class="text-xl font-mono font-bold text-cyan-300">{{ $stats['helpfulPercentage'] }}%</span>
        </div>
    </div>

    <!-- Search & Filter Console -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-lg">
        <form method="GET" action="{{ route('admin.faq.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Query -->
            <div class="sm:col-span-2 relative" style="position: relative;">
                <div style="position: absolute; left: 14px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10;">
                    <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px;"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions, keywords, or answers..." 
                       style="padding-left: 44px !important; padding-right: 16px !important; height: 40px;"
                       class="w-full rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-all">
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category_id" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                    <option value="all">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                    <option value="all">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Drafts Only</option>
                    <option value="featured" {{ request('status') === 'featured' ? 'selected' : '' }}>Featured Only</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold py-2 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.faq.index') }}" class="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white transition-colors" title="Reset Filters">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- FAQs Table Card -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/80 text-[11px] text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Question</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4 text-center">Featured</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Views</th>
                        <th class="py-3 px-4 text-center">Rating</th>
                        <th class="py-3 px-4 text-center">Order</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-slate-800/40 transition-colors group">
                            <!-- Question Title & Excerpt -->
                            <td class="py-3 px-4 max-w-xs sm:max-w-md">
                                <a href="{{ route('admin.faq.edit', $faq->id) }}" class="font-bold text-white hover:text-cyan-400 transition-colors block">
                                    {{ $faq->question }}
                                </a>
                                @if($faq->short_description)
                                    <span class="text-[11px] text-slate-400 block truncate mt-0.5">{{ $faq->short_description }}</span>
                                @endif
                            </td>

                            <!-- Category -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($faq->category)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-cyan-950/60 text-cyan-300 border border-cyan-500/30">
                                        {{ $faq->category->name }}
                                    </span>
                                @else
                                    <span class="text-slate-500 italic">Uncategorized</span>
                                @endif
                            </td>

                            <!-- Featured Badge -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($faq->is_featured)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                        <i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>
                                        <span>Yes</span>
                                    </span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                            </td>

                            <!-- Published Status Toggle Form -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('admin.faq.toggle', $faq->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-transform hover:scale-105 {{ $faq->is_published ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                        {{ $faq->is_published ? 'Published' : 'Draft' }}
                                    </button>
                                </form>
                            </td>

                            <!-- Views Count -->
                            <td class="py-3 px-4 text-center font-mono text-slate-300 whitespace-nowrap">
                                {{ number_format($faq->views_count) }}
                            </td>

                            <!-- Helpful Rating -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @php $pct = $faq->helpfulPercentage(); @endphp
                                <span class="font-mono font-bold {{ $pct >= 75 ? 'text-emerald-400' : ($pct >= 50 ? 'text-amber-400' : 'text-slate-400') }}">
                                    {{ $pct }}%
                                </span>
                                <span class="text-[10px] text-slate-500 block">({{ $faq->helpful_count }}/{{ $faq->helpful_count + $faq->not_helpful_count }})</span>
                            </td>

                            <!-- Sort Order -->
                            <td class="py-3 px-4 text-center font-mono text-slate-400 whitespace-nowrap">
                                {{ $faq->sort_order }}
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('faq.show', $faq->slug) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="View Public Link">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('admin.faq.edit', $faq->id) }}" class="p-1.5 rounded-lg bg-cyan-950 hover:bg-cyan-900 text-cyan-300 hover:text-white border border-cyan-500/30 transition-colors" title="Edit FAQ">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <form action="{{ route('admin.faq.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this FAQ question?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-950 hover:bg-rose-900 text-rose-300 hover:text-white border border-rose-500/30 transition-colors cursor-pointer" title="Delete Question">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-500">
                                <i data-lucide="help-circle" class="w-10 h-10 mx-auto text-slate-700 mb-2"></i>
                                <p class="text-sm font-semibold text-slate-400">No FAQ questions found.</p>
                                <p class="text-xs text-slate-600 mt-0.5">Click "Create FAQ" above to add your first knowledge base article.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faqs->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
