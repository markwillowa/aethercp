@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-800 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Donation Packages &amp; Reward Customizer</h1>
            <p class="text-xs text-slate-400 mt-1">Configure donation tiers, in-game Kafra item rewards, pricing, and player instructions.</p>
        </div>

        <button type="button" onclick="openCreateModal()"
            class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Create New Package</span>
        </button>
    </div>

    <!-- Top Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2 text-xs font-semibold">
        <a href="{{ route('admin.donations') }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 transition-colors flex items-center gap-1.5">
            <i data-lucide="list-checks" class="w-3.5 h-3.5"></i>
            <span>Approvals &amp; Transactions</span>
        </a>
        <a href="{{ route('admin.donations.packages') }}" class="px-3 py-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-1.5">
            <i data-lucide="gift" class="w-3.5 h-3.5"></i>
            <span>Packages, In-Game Rewards &amp; Instructions</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Packages Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-cyan-400"></i>
                <h2 class="text-sm font-bold uppercase tracking-wider text-white">Active Donation Tiers ({{ count($packages) }})</h2>
            </div>
            <span class="text-[10px] text-slate-500 font-mono">Sorted by sort order</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                    <tr>
                        <th class="py-3 px-4">Order</th>
                        <th class="py-3 px-4">Package Name</th>
                        <th class="py-3 px-4">Price</th>
                        <th class="py-3 px-4">Cash Points</th>
                        <th class="py-3 px-4">Bonus Points</th>
                        <th class="py-3 px-4">Bonus In-Game Reward</th>
                        <th class="py-3 px-4 text-center">Badge</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($packages as $pkg)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-4 font-mono text-slate-400">#{{ $pkg->sort_order }}</td>
                            <td class="py-3 px-4">
                                <strong class="text-white font-bold block">{{ $pkg->name }}</strong>
                                <span class="text-[11px] text-slate-400 truncate max-w-xs block">{{ $pkg->description }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-white">
                                ${{ number_format($pkg->price, 2) }} <span class="text-[10px] text-slate-500 uppercase">{{ $pkg->currency }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-cyan-400 font-mono">{{ number_format($pkg->points) }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-400 font-mono">
                                {{ $pkg->bonus_points > 0 ? '+' . number_format($pkg->bonus_points) : '—' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($pkg->item_amount > 0 && ($pkg->item_name || $pkg->item_id))
                                    <div class="flex items-center gap-1.5 text-amber-300 font-medium">
                                        <i data-lucide="gift" class="w-3.5 h-3.5 text-amber-400 shrink-0"></i>
                                        <span>+{{ $pkg->item_amount }}x {{ $pkg->item_name ?: 'Item #' . $pkg->item_id }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500 font-mono block">Item ID: {{ $pkg->item_id }} &bull; Storage Delivery</span>
                                @else
                                    <span class="text-slate-500 italic">Points Only</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($pkg->is_popular)
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-cyan-950 text-cyan-300 border border-cyan-500/40">
                                        Most Popular
                                    </span>
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="{{ route('admin.donations.packages.toggle', $pkg->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase transition-colors {{ $pkg->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-900' : 'bg-slate-800 text-slate-500 border border-slate-700 hover:bg-slate-700' }}">
                                        {{ $pkg->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditModal({{ json_encode($pkg) }})"
                                        class="p-1.5 rounded bg-slate-800 hover:bg-cyan-600 text-slate-300 hover:text-white transition-colors" title="Edit Package">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.donations.packages.delete', $pkg->id) }}" onsubmit="return confirm('Delete this donation tier?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white transition-colors" title="Delete Package">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">No donation tiers configured. Click "Create New Package" to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Instructions & Details Customizer Form -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-6">
        <div>
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-cyan-400"></i>
                <span>Donation Guidelines, Payment Details &amp; Reward Instructions</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                These notices and instructions appear on the public donation page to explain payment methods, claim procedures, and Kafra Storage delivery.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.donations.settings.update') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- General Instructions -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        1. Store Welcome &amp; Donation Notice
                    </label>
                    <textarea name="donation_instructions" rows="4" placeholder="e.g. Welcome to the official donation portal. All funds support server uptime, DDoS shields, and community events..."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 placeholder-slate-600">{{ $instructions }}</textarea>
                    <span class="text-[10px] text-slate-500">Displayed at the top of the donation center.</span>
                </div>

                <!-- Alternative Payment Instructions -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        2. Manual / Alternative Payment Details (Bank / GCash / Crypto)
                    </label>
                    <textarea name="donation_payment_details" rows="4" placeholder="e.g. For GCash or Bank Deposit: Account Name: Freynix Staff | Account No: 0917-XXX-XXXX | After sending, submit your reference number below..."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 placeholder-slate-600">{{ $paymentDetails }}</textarea>
                    <span class="text-[10px] text-slate-500">Displayed above the manual claim form for offline transfers.</span>
                </div>

                <!-- In-Game Kafra Delivery Guide -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        3. Reward Delivery &amp; In-Game Kafra Guide
                    </label>
                    <textarea name="donation_kafra_guide" rows="4" placeholder="e.g. Cash Points (#CASHPOINTS) are credited immediately. Bonus items are deposited directly into your account's Kafra Storage. Talk to any Kafra employee in Prontera to retrieve them..."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 placeholder-slate-600">{{ $kafraGuide }}</textarea>
                    <span class="text-[10px] text-slate-500">Explains how players access their rewarded points and storage items in-game.</span>
                </div>

                <!-- Terms and Refund Policy -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        4. Donation Terms &amp; Refund Policy
                    </label>
                    <textarea name="donation_terms" rows="4" placeholder="e.g. Donations are voluntary gifts given to maintain server costs. Rewards are digital perks provided as a token of appreciation. All contributions are final..."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 placeholder-slate-600">{{ $terms }}</textarea>
                    <span class="text-[10px] text-slate-500">Terms of service notice for contributors.</span>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md transition-colors flex items-center gap-2 cursor-pointer">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save Guidelines &amp; Instructions</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Create / Edit Package Modal -->
<div id="package_modal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 id="modal_title" class="text-sm font-bold text-white flex items-center gap-2">
                <i data-lucide="gift" class="w-4 h-4 text-cyan-400"></i>
                <span>Configure Donation Package</span>
            </h3>
            <button type="button" onclick="closePackageModal()" class="text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="package_form" method="POST" action="{{ route('admin.donations.packages.store') }}" class="p-6 space-y-4">
            @csrf
            <div id="method_container"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Package Name</label>
                    <input type="text" id="pkg_name" name="name" required placeholder="e.g. Valkyrie Grace Tier"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                    <textarea id="pkg_description" name="description" rows="2" placeholder="e.g. Includes bonus Cash Points and Yggdrasil Berries for early adventurers..."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Price (USD / Currency)</label>
                    <input type="number" id="pkg_price" name="price" step="0.01" min="0.01" required placeholder="25.00"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Currency Code</label>
                    <input type="text" id="pkg_currency" name="currency" value="USD" required placeholder="USD"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 uppercase font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Base Cash Points</label>
                    <input type="number" id="pkg_points" name="points" min="0" required placeholder="2500"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Bonus Cash Points</label>
                    <input type="number" id="pkg_bonus_points" name="bonus_points" min="0" value="0" placeholder="500"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 font-mono">
                </div>
            </div>

            <!-- In-Game Item Reward Section -->
            <div class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/20 space-y-3">
                <div class="flex items-center gap-1.5 text-xs font-bold text-amber-300">
                    <i data-lucide="package-plus" class="w-4 h-4 text-amber-400"></i>
                    <span>Bonus In-Game Item Reward (Delivered to Kafra Storage)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Item ID</label>
                        <input type="number" id="pkg_item_id" name="item_id" min="1" placeholder="e.g. 607"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200 font-mono">
                        <span class="text-[9px] text-slate-500">e.g. 607 (Yggdrasil Berry)</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Amount</label>
                        <input type="number" id="pkg_item_amount" name="item_amount" min="0" value="0" placeholder="15"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200 font-mono">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Display Item Name</label>
                        <input type="text" id="pkg_item_name" name="item_name" placeholder="Yggdrasil Berry"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Sort Order</label>
                    <input type="number" id="pkg_sort_order" name="sort_order" value="1"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 font-mono">
                </div>

                <div class="flex items-center gap-2 pt-5">
                    <input type="checkbox" id="pkg_is_popular" name="is_popular" value="1"
                        class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                    <label for="pkg_is_popular" class="text-xs font-semibold text-slate-300">Highlight as Popular</label>
                </div>

                <div class="flex items-center gap-2 pt-5">
                    <input type="checkbox" id="pkg_is_active" name="is_active" value="1" checked
                        class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                    <label for="pkg_is_active" class="text-xs font-semibold text-slate-300">Active &amp; Visible</label>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closePackageModal()"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold shadow-md">
                    Save Donation Package
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modal_title').innerText = 'Create New Donation Package';
        document.getElementById('package_form').action = "{{ route('admin.donations.packages.store') }}";
        document.getElementById('method_container').innerHTML = '';
        
        document.getElementById('pkg_name').value = '';
        document.getElementById('pkg_description').value = '';
        document.getElementById('pkg_price').value = '';
        document.getElementById('pkg_currency').value = 'USD';
        document.getElementById('pkg_points').value = '';
        document.getElementById('pkg_bonus_points').value = '0';
        document.getElementById('pkg_item_id').value = '';
        document.getElementById('pkg_item_amount').value = '0';
        document.getElementById('pkg_item_name').value = '';
        document.getElementById('pkg_sort_order').value = '1';
        document.getElementById('pkg_is_popular').checked = false;
        document.getElementById('pkg_is_active').checked = true;

        document.getElementById('package_modal').classList.remove('hidden');
    }

    function openEditModal(pkg) {
        document.getElementById('modal_title').innerText = 'Edit Package: ' + pkg.name;
        document.getElementById('package_form').action = "/admin/donations/packages/" + pkg.id;
        document.getElementById('method_container').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('pkg_name').value = pkg.name || '';
        document.getElementById('pkg_description').value = pkg.description || '';
        document.getElementById('pkg_price').value = pkg.price || '';
        document.getElementById('pkg_currency').value = pkg.currency || 'USD';
        document.getElementById('pkg_points').value = pkg.points || '';
        document.getElementById('pkg_bonus_points').value = pkg.bonus_points || '0';
        document.getElementById('pkg_item_id').value = pkg.item_id || '';
        document.getElementById('pkg_item_amount').value = pkg.item_amount || '0';
        document.getElementById('pkg_item_name').value = pkg.item_name || '';
        document.getElementById('pkg_sort_order').value = pkg.sort_order || '0';
        document.getElementById('pkg_is_popular').checked = !!pkg.is_popular;
        document.getElementById('pkg_is_active').checked = !!pkg.is_active;

        document.getElementById('package_modal').classList.remove('hidden');
    }

    function closePackageModal() {
        document.getElementById('package_modal').classList.add('hidden');
    }
</script>
@endsection

