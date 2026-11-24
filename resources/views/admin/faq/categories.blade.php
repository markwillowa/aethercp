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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="folder" class="w-6 h-6 text-cyan-400"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide uppercase">FAQ Categories</h1>
                <p class="text-xs text-slate-400">Organize your server knowledge base into logical browsing sections.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.faq.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to FAQs</span>
            </a>
            <button type="button" @click="createModal = true" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold py-2 px-4 rounded-xl text-xs uppercase tracking-wider flex items-center gap-1.5 shadow-md cursor-pointer transition-all">
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
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/80 text-[11px] text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Icon</th>
                        <th class="py-3 px-4">Name &amp; Slug</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4 text-center">Questions</th>
                        <th class="py-3 px-4 text-center">Sort Order</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-800/40 transition-colors group">
                            <!-- Icon -->
                            <td class="py-3 px-4 w-12 text-center">
                                <div class="w-8 h-8 rounded-lg bg-cyan-950/40 border border-cyan-500/30 text-cyan-400 flex items-center justify-center mx-auto">
                                    <i data-lucide="{{ $cat->icon ?: 'folder' }}" class="w-4 h-4"></i>
                                </div>
                            </td>

                            <!-- Name & Slug -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-white block">{{ $cat->name }}</span>
                                <span class="font-mono text-[10px] text-cyan-400">/faq/category/{{ $cat->slug }}</span>
                            </td>

                            <!-- Description -->
                            <td class="py-3 px-4 max-w-xs text-slate-400 truncate">
                                {{ $cat->description ?: '—' }}
                            </td>

                            <!-- Question Count -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-800 text-slate-300">
                                    {{ $cat->faqs_count }}
                                </span>
                            </td>

                            <!-- Sort Order -->
                            <td class="py-3 px-4 text-center font-mono text-slate-400 whitespace-nowrap">
                                {{ $cat->sort_order }}
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $cat->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                                    {{ $cat->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="openEdit({{ $cat->toJson() }})" class="p-1.5 rounded-lg bg-cyan-950 hover:bg-cyan-900 text-cyan-300 border border-cyan-500/30 transition-colors cursor-pointer" title="Edit Category">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <form action="{{ route('admin.faq.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete category {{ $cat->name }}? All related FAQs will need reassignment.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-950 hover:bg-rose-900 text-rose-300 border border-rose-500/30 transition-colors cursor-pointer" title="Delete Category">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-500">
                                <p class="text-sm font-semibold text-slate-400">No categories created yet.</p>
                                <p class="text-xs text-slate-600 mt-0.5">Click "Add Category" to create your first knowledge base section.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Category Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl" @click.away="createModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wide">Add New Category</h3>
                <button type="button" @click="createModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('admin.faq.categories.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Account & Security" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief summary of topics in this category..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Lucide Icon Name</label>
                        <input type="text" name="icon" value="folder" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer pt-1">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                    <span class="text-slate-300 font-medium">Visible to Players</span>
                </label>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl" @click.away="editModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wide">Edit Category</h3>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form :action="'{{ url('admin/faq/categories') }}/' + editCat.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Category Name *</label>
                    <input type="text" name="name" x-model="editCat.name" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Slug</label>
                    <input type="text" name="slug" x-model="editCat.slug" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="2" x-model="editCat.description" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Lucide Icon Name</label>
                        <input type="text" name="icon" x-model="editCat.icon" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="editCat.sort_order" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer pt-1">
                    <input type="checkbox" name="is_active" value="1" x-model="editCat.is_active" class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                    <span class="text-slate-300 font-medium">Visible to Players</span>
                </label>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold">Update Category</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
