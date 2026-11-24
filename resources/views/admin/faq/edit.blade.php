@extends('layouts.admin')

@section('title', 'Edit FAQ Question')

@section('admin_content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#1C1425] border border-[#4A3A5A] shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-[#D4AF37]/20 border border-[#D4AF37]/50 text-[#FCE69B] flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="edit-3" class="w-6 h-6 text-[#FFD700]"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-white font-cinzel tracking-wide uppercase">Edit FAQ Question</h1>
                <p class="text-xs text-[#C8BDAA]">Update knowledge base content and classification.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('faq.show', $faq->slug) }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#5A456B] text-[#FFE894] hover:bg-[#251A33] hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>View Public</span>
            </a>
            <a href="{{ route('admin.faq.index') }}" class="px-4 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#5A456B] text-[#FFE894] hover:bg-[#251A33] hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back</span>
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

    <!-- Edit Form -->
    <form action="{{ route('admin.faq.update', $faq->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Core Information -->
        <div class="rounded-2xl bg-[#1C1425] border border-[#3E2F4D] p-5 shadow-lg space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-[#352643]">
                <i data-lucide="help-circle" class="w-5 h-5 text-[#D4AF37]"></i>
                <h2 class="text-sm font-bold text-white font-cinzel uppercase tracking-wide">Question &amp; Classification</h2>
            </div>

            <!-- Question Title -->
            <div>
                <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Question Title *</label>
                <input type="text" name="question" value="{{ old('question', $faq->question) }}" required placeholder="e.g. How do I recover my character password?" 
                       class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37] transition-all">
            </div>

            <!-- Category, Status, Featured in 3 cols -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Category</label>
                    <select name="category_id" class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37] transition-all">
                        <option value="">-- Uncategorized --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ old('category_id', $faq->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Publication Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37] transition-all">
                        <option value="published" {{ old('status', $faq->status) === 'published' ? 'selected' : '' }}>Published (Public)</option>
                        <option value="draft" {{ old('status', $faq->status) === 'draft' ? 'selected' : '' }}>Draft (Admin Only)</option>
                        <option value="archived" {{ old('status', $faq->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-white">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $faq->is_featured) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#352643] text-[#D4AF37] focus:ring-[#D4AF37]">
                        <span class="font-bold">Feature on FAQ Home</span>
                    </label>
                </div>
            </div>

            <!-- Custom Slug & Keywords -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">URL Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $faq->slug) }}" required 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white font-mono focus:outline-none focus:border-[#D4AF37] transition-all">
                </div>

                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Search Keywords (Tags)</label>
                    <input type="text" name="keywords" value="{{ old('keywords', $faq->keywords) }}" placeholder="e.g. password, recovery, pin, security" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37] transition-all">
                </div>
            </div>

            <!-- Short Summary -->
            <div>
                <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Short Description (Optional)</label>
                <input type="text" name="short_description" value="{{ old('short_description', $faq->short_description) }}" placeholder="Brief 1-sentence teaser or summary" 
                       class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37] transition-all">
            </div>
        </div>

        <!-- Answer Content (HTML / Rich Text) -->
        <div class="rounded-2xl bg-[#1C1425] border border-[#3E2F4D] p-5 shadow-lg space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#352643]">
                <div class="flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-[#D4AF37]"></i>
                    <h2 class="text-sm font-bold text-white font-cinzel uppercase tracking-wide">Answer Content *</h2>
                </div>
                <span class="text-[11px] text-[#A898B5]">HTML formatted text supported (&lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;code&gt;)</span>
            </div>

            <div>
                <textarea name="answer" rows="10" required placeholder="Type the complete detailed explanation or guide..." 
                          class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37] font-mono leading-relaxed transition-all">{{ old('answer', $faq->answer) }}</textarea>
            </div>
        </div>

        <!-- Related Questions Multi-Select -->
        @if($otherFaqs->count() > 0)
            @php
                $existingRelated = $faq->relatedQuestions->pluck('id')->toArray();
            @endphp
            <div class="rounded-2xl bg-[#1C1425] border border-[#3E2F4D] p-5 shadow-lg space-y-3">
                <div class="flex items-center gap-2 pb-3 border-b border-[#352643]">
                    <i data-lucide="link" class="w-5 h-5 text-[#D4AF37]"></i>
                    <h2 class="text-sm font-bold text-white font-cinzel uppercase tracking-wide">Related Questions</h2>
                </div>
                <p class="text-[11px] text-[#A898B5]">Select related questions to recommend to players under this answer:</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 rounded-xl bg-[#140E1B] border border-[#352643]">
                    @foreach($otherFaqs as $other)
                        @php
                            $isChecked = is_array(old('related_ids')) ? in_array($other->id, old('related_ids')) : in_array($other->id, $existingRelated);
                        @endphp
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[#251A33] cursor-pointer text-xs text-slate-300">
                            <input type="checkbox" name="related_ids[]" value="{{ $other->id }}" {{ $isChecked ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 rounded text-[#D4AF37] border-[#352643]">
                            <span class="truncate">{{ $other->question }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- SEO Metadata -->
        <div class="rounded-2xl bg-[#1C1425] border border-[#3E2F4D] p-5 shadow-lg space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-[#352643]">
                <i data-lucide="globe" class="w-5 h-5 text-[#D4AF37]"></i>
                <h2 class="text-sm font-bold text-white font-cinzel uppercase tracking-wide">SEO &amp; Search Engine Optimization</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">SEO Meta Title</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $faq->seo_title) }}" placeholder="Custom page title tag" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37] transition-all">
                </div>

                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">SEO Meta Description</label>
                    <input type="text" name="seo_description" value="{{ old('seo_description', $faq->seo_description) }}" placeholder="Brief summary snippet for Google search" 
                           class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37] transition-all">
                </div>
            </div>
        </div>

        <!-- Submit Row -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.faq.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#4A3A5A] text-[#C8BDAA] hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-cinzel font-black uppercase tracking-wider bg-gradient-to-b from-[#FFE894] to-[#8C5E09] text-[#1A1202] border border-[#FFEAA5] hover:brightness-110 shadow-lg cursor-pointer flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Update FAQ</span>
            </button>
        </div>
    </form>
</div>
@endsection
