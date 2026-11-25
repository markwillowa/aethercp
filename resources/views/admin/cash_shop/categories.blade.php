@extends('layouts.admin')

@section('title', 'Cash Shop Categories')

@section('content')
<div class="space-y-6" x-data="{
    modalOpen: false,
    editMode: false,
    categoryId: null,
    formAction: '{{ route('admin.cash-shop.categories.store') }}',
    name: '',
    icon: 'tag',
    description: '',
    sort_order: 0,
    is_active: true,
    is_featured: false,

    openCreate() {
        this.editMode = false;
        this.categoryId = null;
        this.formAction = '{{ route('admin.cash-shop.categories.store') }}';
        this.name = '';
        this.icon = 'tag';
        this.description = '';
        this.sort_order = 0;
        this.is_active = true;
        this.is_featured = false;
        this.modalOpen = true;
    },

    openEdit(cat) {
        this.editMode = true;
        this.categoryId = cat.id;
        this.formAction = '/admin/cash-shop/categories/' + cat.id;
        this.name = cat.name;
        this.icon = cat.icon || 'tag';
        this.description = cat.description || '';
        this.sort_order = cat.sort_order || 0;
        this.is_active = !!cat.is_active;
        this.is_featured = !!cat.is_featured;
        this.modalOpen = true;
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.cash-shop.index') }}" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white">Item Mall Categories</h1>
                <p class="text-xs text-slate-400 mt-0.5">Organize items into browsable departments (Consumables, Gear, Cards, Costumes).</p>
            </div>
        </div>

        <button type="button" @click="openCreate()" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md shadow-amber-500/20 transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Category</span>
        </button>
    </div>

    <!-- Category Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Category Name</th>
                        <th class="py-3.5 px-4">Slug</th>
                        <th class="py-3.5 px-4 text-center">Products Count</th>
                        <th class="py-3.5 px-4 text-center">Sort Order</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                                        <i data-lucide="{{ $cat->icon ?: 'tag' }}" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-white text-sm">{{ $cat->name }}</span>
                                        @if($cat->description)
                                            <p class="text-[10px] text-slate-400 line-clamp-1">{{ $cat->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400">
                                {{ $cat->slug }}
                            </td>

                            <td class="py-3.5 px-4 text-center font-mono font-bold text-cyan-300">
                                {{ $cat->products_count }}
                            </td>

                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">
                                {{ $cat->sort_order }}
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $cat->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                                    {{ $cat->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="openEdit({{ $cat->toJson() }})" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer" title="Edit Category">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    @if($cat->products_count == 0)
                                        <form action="{{ route('admin.cash-shop.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 transition-colors cursor-pointer" title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i data-lucide="folder-kanban" class="w-10 h-10 text-slate-600 mx-auto mb-2"></i>
                                <p class="text-sm font-bold text-white">No categories created yet</p>
                                <p class="text-xs text-slate-500 mt-1">Create categories to organize your web Item Mall products.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Category Modal -->
    <div x-show="modalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="modalOpen = false" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white" x-text="editMode ? 'Edit Category' : 'Create Category'"></h3>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Category Name <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" x-model="name" required placeholder="e.g. Consumables &amp; Buffs"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Lucide Icon</label>
                        <input type="text" name="icon" x-model="icon" placeholder="tag, shield, sword, gem..."
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white font-mono focus:outline-none focus:border-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="sort_order"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white font-mono focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description (Optional)</label>
                    <textarea name="description" x-model="description" rows="2" placeholder="Brief category summary..."
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-slate-200 placeholder:text-slate-600 focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <div class="flex items-center gap-4 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="is_active" class="rounded bg-slate-950 border-slate-800 text-amber-500">
                        <span class="text-xs text-slate-200 font-semibold">Active &amp; Visible</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" x-model="is_featured" class="rounded bg-slate-950 border-slate-800 text-amber-500">
                        <span class="text-xs text-slate-200 font-semibold">Featured</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 transition-colors" x-text="editMode ? 'Update' : 'Create'"></button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
