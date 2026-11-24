@extends('layouts.admin')

@section('title', 'Create FAQ Question')

@section('admin_content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="plus-circle" class="w-6 h-6 text-cyan-400"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide uppercase">Create FAQ Question</h1>
                <p class="text-xs text-slate-400">Compose a new knowledge base article for players.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.faq.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to FAQs</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs space-y-1 shadow-lg">
            <div class="font-bold flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                <span>Please fix the following validation errors:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create Form -->
    <form action="{{ route('admin.faq.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Core Information -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="help-circle" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wide">Question &amp; Classification</h2>
            </div>

            <!-- Question Title -->
            <div>
                <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Question Title *</label>
                <input type="text" name="question" value="{{ old('question') }}" required placeholder="e.g. How do I recover my character password?" 
                       class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
            </div>

            <!-- Category, Status, Featured in 3 cols -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Category</label>
                    <select name="category_id" class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <option value="">-- Uncategorized --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Publication Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Public)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Admin Only)</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-white">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-cyan-500 focus:ring-0">
                        <span class="font-bold">Feature on FAQ Home</span>
                    </label>
                </div>
            </div>

            <!-- Custom Slug & Keywords -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Custom Slug (Optional)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="Leave blank to auto-generate from question" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white font-mono focus:outline-none focus:border-cyan-500 transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Search Keywords (Tags)</label>
                    <input type="text" name="keywords" value="{{ old('keywords') }}" placeholder="e.g. password, recovery, pin, security" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                </div>
            </div>

            <!-- Short Summary -->
            <div>
                <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">Short Description (Optional)</label>
                <input type="text" name="short_description" value="{{ old('short_description') }}" placeholder="Brief 1-sentence teaser or summary" 
                       class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
            </div>
        </div>

        <!-- Answer Content (HTML / Rich Text) -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-cyan-400"></i>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wide">Answer Content *</h2>
                </div>
                <span class="text-[11px] text-slate-400">HTML formatted text supported (&lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;code&gt;)</span>
            </div>

            <div>
                <textarea name="answer" rows="10" required placeholder="Type the complete detailed explanation or guide..." 
                          class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 font-mono leading-relaxed transition-all">{{ old('answer') }}</textarea>
            </div>
        </div>

        <!-- Related Questions Multi-Select -->
        @if($otherFaqs->count() > 0)
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg space-y-3">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                    <i data-lucide="link" class="w-5 h-5 text-cyan-400"></i>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wide">Related Questions</h2>
                </div>
                <p class="text-[11px] text-slate-400">Select related questions to recommend to players under this answer:</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 rounded-xl bg-slate-950 border border-slate-800">
                    @foreach($otherFaqs as $other)
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-slate-900/80 cursor-pointer text-xs text-slate-300">
                            <input type="checkbox" name="related_ids[]" value="{{ $other->id }}" 
                                   {{ is_array(old('related_ids')) && in_array($other->id, old('related_ids')) ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 rounded text-cyan-500 border-slate-700 bg-slate-950 focus:ring-0">
                            <span class="truncate">{{ $other->question }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- SEO Metadata -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
                <i data-lucide="globe" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wide">SEO &amp; Search Engine Optimization</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">SEO Meta Title</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title') }}" placeholder="Custom page title tag" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-1">SEO Meta Description</label>
                    <input type="text" name="seo_description" value="{{ old('seo_description') }}" placeholder="Brief summary snippet for Google search" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Submit Row -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.faq.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-300 hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg cursor-pointer flex items-center gap-2 transition-all">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Publish FAQ</span>
            </button>
        </div>
    </form>
</div>
@endsection
