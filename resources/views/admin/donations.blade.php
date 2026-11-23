@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Donation Management</h1>
        <p class="text-xs text-slate-400 mt-1">Review player donation submissions, PayPal IPN payments, and in-game reward fulfillment.</p>
    </div>

    <!-- Top Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2 text-xs font-semibold">
        <a href="{{ route('admin.donations') }}" class="px-3 py-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-1.5">
            <i data-lucide="list-checks" class="w-3.5 h-3.5"></i>
            <span>Approvals &amp; Transactions</span>
        </a>
        <a href="{{ route('admin.donations.packages') }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 transition-colors flex items-center gap-1.5">
            <i data-lucide="gift" class="w-3.5 h-3.5"></i>
            <span>Packages, In-Game Rewards &amp; Instructions</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Account</th>
                        <th class="py-3 px-4">Package</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Points</th>
                        <th class="py-3 px-4">Storage Items</th>
                        <th class="py-3 px-4">Method / Ref</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">In-Game Delivery</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($donations as $d)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400">#{{ $d->id }}</td>
                            <td class="py-3 px-4 font-mono text-slate-300">#{{ $d->account_id }}</td>
                            <td class="py-3 px-4 font-bold text-white">{{ $d->package?->name ?? 'Custom Tier' }}</td>
                            <td class="py-3 px-4 font-mono">${{ number_format($d->amount, 2) }}</td>
                            <td class="py-3 px-4 font-bold text-cyan-400">{{ number_format($d->points_awarded) }}</td>
                            <td class="py-3 px-4">
                                @if($d->package && $d->package->item_amount > 0)
                                    <span class="text-amber-400 font-medium">{{ $d->package->item_amount }}x {{ $d->package->item_name ?: 'Item #'.$d->package->item_id }}</span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-mono text-slate-300 text-[11px]">{{ $d->transaction_reference }}</div>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $d->payment_method === 'paypal' ? 'bg-blue-950 text-blue-300 border border-blue-500/30' : 'bg-slate-800 text-slate-400' }}">
                                    {{ $d->payment_method }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $d->status === 'approved' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : ($d->status === 'pending' ? 'bg-amber-950 text-amber-400 border border-amber-500/30' : 'bg-red-950 text-red-400 border border-red-500/30') }}">
                                    {{ $d->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($d->status === 'approved')
                                    <div class="flex flex-col items-center gap-0.5 text-[10px]">
                                        <span class="{{ $d->points_delivered ? 'text-emerald-400' : 'text-amber-400' }}">
                                            Points: {{ $d->points_delivered ? 'Credited' : 'Pending' }}
                                        </span>
                                        @if($d->package && $d->package->item_amount > 0)
                                            <span class="{{ $d->items_delivered ? 'text-emerald-400' : 'text-amber-400' }}">
                                                Items: {{ $d->items_delivered ? 'Delivered' : 'Pending' }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-500 text-[10px]">Awaiting</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($d->status === 'pending')
                                    <div class="flex items-center justify-end gap-1">
                                        <form method="POST" action="{{ route('admin.donations.status', $d->id) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2 py-1 rounded bg-emerald-950 hover:bg-emerald-900 text-emerald-300 font-semibold text-[10px] border border-emerald-500/30 cursor-pointer">
                                                Approve & Deliver
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.donations.status', $d->id) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2 py-1 rounded bg-red-950 hover:bg-red-900 text-red-300 font-semibold text-[10px] border border-red-500/30 cursor-pointer">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-slate-500 text-[10px]">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-slate-500">No donation transactions recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        {{ $donations->links() }}
    </div>
</div>
@endsection
