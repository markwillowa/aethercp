@extends('layouts.admin')

@section('title', 'FAQ & Knowledge Base Management')

@section('admin_content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#1C1425] border border-[#4A3A5A] shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-[#D4AF37]/20 border border-[#D4AF37]/50 text-[#FCE69B] flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="help-circle" class="w-6 h-6 text-[#FFD700]"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-white font-cinzel tracking-wide uppercase">FAQ &amp; Knowledge Base</h1>
                <p class="text-xs text-[#C8BDAA]">Publish categorized guides, track player feedback, and manage documentation.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.faq.categories') }}" class="px-4 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#5A456B] text-[#FFE894] hover:bg-[#251A33] hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="folder" class="w-4 h-4 text-[#D4AF37]"></i>
                <span>Categories ({{ $stats['categoriesCount'] }})</span>
            </a>
            <a href="{{ route('admin.faq.create') }}" class="chronicles-btn-gold py-2 px-4 text-xs font-cinzel font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-md">
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
        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-[#3E2F4D]">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] block mb-1">Total FAQs</span>
            <span class="text-xl font-mono font-bold text-white">{{ number_format($stats['totalFaqs']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-emerald-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">Published</span>
            <span class="text-xl font-mono font-bold text-emerald-300">{{ number_format($stats['published']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-amber-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 block mb-1">Drafts</span>
            <span class="text-xl font-mono font-bold text-amber-300">{{ number_format($stats['draft']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-cyan-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 block mb-1">Categories</span>
            <span class="text-xl font-mono font-bold text-cyan-300">{{ number_format($stats['categoriesCount']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-purple-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400 block mb-1">Total Views</span>
            <span class="text-xl font-mono font-bold text-purple-300">{{ number_format($stats['totalViews']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-[#D4AF37]/40">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#FFE894] block mb-1">Helpful Rating</span>
            <span class="text-xl font-mono font-bold text-[#FFD700]">{{ $stats['helpfulPercentage'] }}%</span>
        </div>
    </div>

    <!-- Search & Filter Console -->
    <div class="p-4 rounded-2xl bg-[#1C1425] border border-[#3E2F4D] shadow-lg">
        <form method="GET" action="{{ route('admin.faq.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Query -->
            <div class="sm:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-[#C9A96E]"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions, keywords, or answers..." 
                       style="padding-left: 3rem !important; padding-right: 1rem !important; height: 40px;"
                       class="w-full rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white placeholder-slate-500 focus:outline-none focus:border-[#D4AF37] transition-all">
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category_id" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                    <option value="all">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                    <option value="all">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <!-- Filter Submit & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold font-cinzel bg-[#D4AF37] hover:bg-[#FCE69B] text-black transition-all cursor-pointer">
                    Filter
                </button>
                @if(request('search') || (request('category_id') && request('category_id') !== 'all') || (request('status') && request('status') !== 'all'))
                    <a href="{{ route('admin.faq.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-[#251A33] border border-[#3E2F4D] transition-all" title="Reset Filters">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- FAQs Management Table -->
    <div class="rounded-2xl bg-[#1C1425] border border-[#3E2F4D] overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-[#352643] bg-[#140E1B]/80 font-cinzel text-[11px] text-[#D4AF37] uppercase tracking-wider">
                        <th class="p-3.5 pl-4">Question</th>
                        <th class="p-3.5">Category</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-center">Featured</th>
                        <th class="p-3.5 text-right">Views</th>
                        <th class="p-3.5 text-center">Helpful</th>
                        <th class="p-3.5">Updated</th>
                        <th class="p-3.5 pr-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B1D38]">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-[#231830] transition-colors">
                            <!-- Question & Slug -->
                            <td class="p-3.5 pl-4 max-w-sm">
                                <a href="{{ route('admin.faq.edit', $faq->id) }}" class="font-bold text-white hover:text-[#FFE894] transition-colors block">
                                    {{ $faq->question }}
                                </a>
                                <span class="text-[10px] text-[#8C7B99] font-mono block mt-0.5 truncate">/faq/{{ $faq->slug }}</span>
                            </td>

                            <!-- Category -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if($faq->category)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium bg-[#291D38] border border-[#5A456B] text-[#E2D9EB]">
                                        <i data-lucide="{{ $faq->category->icon ?: 'folder' }}" class="w-3 h-3 text-[#D4AF37]"></i>
                                        <span>{{ $faq->category->name }}</span>
                                    </span>
                                @else
                                    <span class="text-[#8C7B99] italic">Uncategorized</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if($faq->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-950/70 text-emerald-300 border border-emerald-500/40">
                                        Published
                                    </span>
                                @elseif($faq->status === 'draft')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-950/70 text-amber-300 border border-amber-500/40">
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                                        Archived
                                    </span>
                                @endif
                            </td>

                            <!-- Featured -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($faq->is_featured)
                                    <i data-lucide="star" class="w-4 h-4 text-[#FFD700] mx-auto fill-current"></i>
                                @else
                                    <span class="text-[#564766] font-mono">-</span>
                                @endif
                            </td>

                            <!-- Views -->
                            <td class="p-3.5 text-right font-mono font-medium text-[#C8BDAA] whitespace-nowrap">
                                {{ number_format($faq->views_count) }}
                            </td>

                            <!-- Helpful Percentage -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="font-mono font-bold {{ $faq->helpfulPercentage() >= 80 ? 'text-emerald-400' : 'text-amber-400' }}">
                                    {{ $faq->helpfulPercentage() }}%
                                </span>
                                <span class="text-[10px] text-[#8C7B99] block font-mono">({{ $faq->helpful_count }}/{{ $faq->total_feedback_count }})</span>
                            </td>

                            <!-- Updated -->
                            <td class="p-3.5 whitespace-nowrap text-[11px] text-[#A898B5] font-mono">
                                {{ $faq->updated_at->format('M d, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="p-3.5 pr-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('faq.show', $faq->slug) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-[#251A33] transition-colors" title="View Public FAQ">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.faq.edit', $faq->id) }}" class="p-1.5 rounded-lg text-cyan-400 hover:text-cyan-300 hover:bg-[#251A33] transition-colors" title="Edit Question">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.faq.destroy', $faq->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this FAQ question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-950/30 transition-colors cursor-pointer" title="Delete Question">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center text-[#8C7B99]">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto text-[#5A456B] mb-2"></i>
                                <p class="text-sm font-bold text-white">No FAQ questions found</p>
                                <p class="text-xs mt-1">Create your first question or adjust filter parameters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faqs->hasPages())
            <div class="p-4 border-t border-[#352643]">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
