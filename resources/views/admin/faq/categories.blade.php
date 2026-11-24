@extends('layouts.admin')

@section('title', 'FAQ Category Management')

@section('admin_content')
<div class="space-y-6" x-data="{
    createModal: false,
    editModal: false,
    editCat: { id: null, name: '', slug: '', description: '', icon: 'help-circle', sort_order: 0, is_active: true },
    openEdit(cat) {
        this.editCat = { ...cat };
        this.editModal = true;
    }
}">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#1C1425] border border-[#4A3A5A] shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-[#D4AF37]/20 border border-[#D4AF37]/50 text-[#FCE69B] flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="folder" class="w-6 h-6 text-[#FFD700]"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-white font-cinzel tracking-wide uppercase">FAQ Categories</h1>
                <p class="text-xs text-[#C8BDAA]">Organize your server knowledge base into logical browsing sections.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.faq.index') }}" class="px-4 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#5A456B] text-[#FFE894] hover:bg-[#251A33] hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to FAQs</span>
            </a>
            <button type="button" @click="createModal = true" class="chronicles-btn-gold py-2 px-4 text-xs font-cinzel font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-md cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Category</span>
            </button>
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

    <!-- Categories Table -->
    <div class="rounded-2xl bg-[#1C1425] border border-[#3E2F4D] overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-[#352643] bg-[#140E1B]/80 font-cinzel text-[11px] text-[#D4AF37] uppercase tracking-wider">
                        <th class="p-3.5 pl-4">Category Name</th>
                        <th class="p-3.5">Slug</th>
                        <th class="p-3.5 text-center">Icon</th>
                        <th class="p-3.5 text-center">Sort Order</th>
                        <th class="p-3.5 text-center">Status</th>
                        <th class="p-3.5 text-center">Questions</th>
                        <th class="p-3.5 pr-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B1D38]">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-[#231830] transition-colors">
                            <!-- Name & Description -->
                            <td class="p-3.5 pl-4 max-w-sm">
                                <span class="font-bold text-white block">{{ $cat->name }}</span>
                                <span class="text-[11px] text-[#A898B5] block mt-0.5 truncate">{{ $cat->description ?: 'No description provided' }}</span>
                            </td>

                            <!-- Slug -->
                            <td class="p-3.5 font-mono text-[11px] text-[#D4AF37]">
                                {{ $cat->slug }}
                            </td>

                            <!-- Icon -->
                            <td class="p-3.5 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#251A33] border border-[#4A3A5A] text-[#FFE894]">
                                    <i data-lucide="{{ $cat->icon ?: 'folder' }}" class="w-4 h-4"></i>
                                </span>
                            </td>

                            <!-- Sort Order -->
                            <td class="p-3.5 text-center font-mono text-white">
                                {{ $cat->sort_order }}
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center">
                                @if($cat->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-950/70 text-emerald-300 border border-emerald-500/40">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-400 border border-slate-700">
                                        Hidden
                                    </span>
                                @endif
                            </td>

                            <!-- Question Count -->
                            <td class="p-3.5 text-center font-mono font-bold text-[#FFD700]">
                                {{ $cat->faqs_count }}
                            </td>

                            <!-- Actions -->
                            <td class="p-3.5 pr-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click="openEdit({{ $cat->toJson() }})" class="p-1.5 rounded-lg text-cyan-400 hover:text-cyan-300 hover:bg-[#251A33] transition-colors cursor-pointer" title="Edit Category">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Delete Category Form -->
                                    <form method="POST" action="{{ route('admin.faq.categories.destroy', $cat->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete category \'{{ $cat->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-950/30 transition-colors cursor-pointer" title="Delete Category">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#8C7B99]">
                                <i data-lucide="folder-x" class="w-10 h-10 mx-auto text-[#5A456B] mb-2"></i>
                                <p class="text-sm font-bold text-white">No categories created</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Category Modal -->
    <div x-show="createModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div @click.away="createModal = false" class="rounded-2xl bg-[#1C1425] border-2 border-[#5A456B] p-6 max-w-lg w-full space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-[#352643]">
                <h3 class="text-sm font-bold text-white font-cinzel uppercase tracking-wide flex items-center gap-2">
                    <i data-lucide="folder-plus" class="w-4 h-4 text-[#D4AF37]"></i>
                    <span>Add FAQ Category</span>
                </h3>
                <button type="button" @click="createModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.faq.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Account Security" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Lucide Icon</label>
                        <input type="text" name="icon" value="help-circle" placeholder="e.g. user-check, swords, gem" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Sort Order</label>
                        <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Short Description</label>
                    <textarea name="description" rows="2" placeholder="Brief summary of topics in this category..." class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" checked id="create_active" class="w-4 h-4 rounded border-[#352643] text-[#D4AF37]">
                    <label for="create_active" class="text-xs font-bold text-white cursor-pointer">Active (Visible to Players)</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#352643]">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl text-xs font-cinzel bg-[#140E1B] border border-[#4A3A5A] text-slate-300 hover:text-white cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#D4AF37] hover:bg-[#FCE69B] text-black cursor-pointer shadow">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="editModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div @click.away="editModal = false" class="rounded-2xl bg-[#1C1425] border-2 border-[#5A456B] p-6 max-w-lg w-full space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-[#352643]">
                <h3 class="text-sm font-bold text-white font-cinzel uppercase tracking-wide flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4 text-[#D4AF37]"></i>
                    <span>Edit Category</span>
                </h3>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'{{ url('/admin/faq/categories') }}/' + editCat.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Category Name *</label>
                    <input type="text" name="name" x-model="editCat.name" required class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Lucide Icon</label>
                        <input type="text" name="icon" x-model="editCat.icon" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Sort Order</label>
                        <input type="number" name="sort_order" x-model.number="editCat.sort_order" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Short Description</label>
                    <textarea name="description" x-model="editCat.description" rows="2" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" :checked="editCat.is_active" id="edit_active" class="w-4 h-4 rounded border-[#352643] text-[#D4AF37]">
                    <label for="edit_active" class="text-xs font-bold text-white cursor-pointer">Active (Visible to Players)</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#352643]">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl text-xs font-cinzel bg-[#140E1B] border border-[#4A3A5A] text-slate-300 hover:text-white cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#D4AF37] hover:bg-[#FCE69B] text-black cursor-pointer shadow">Update Category</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
