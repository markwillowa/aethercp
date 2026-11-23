@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Donation Management</h1>
        <p class="text-xs text-slate-400 mt-1">Review player donation submissions and approve cash shop points.</p>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                <tr>
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Account ID</th>
                    <th class="py-3 px-4">Package</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Points</th>
                    <th class="py-3 px-4">Reference</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($donations as $d)
                    <tr class="hover:bg-slate-800/30">
                        <td class="py-3 px-4 font-mono font-bold text-cyan-400">#{{ $d->id }}</td>
                        <td class="py-3 px-4 font-mono text-slate-300">#{{ $d->account_id }}</td>
                        <td class="py-3 px-4 font-bold text-white">{{ $d->package?->name ?? 'Custom' }}</td>
                        <td class="py-3 px-4 font-mono">${{ number_format($d->amount, 2) }}</td>
                        <td class="py-3 px-4 font-bold text-cyan-400">{{ number_format($d->points_awarded) }}</td>
                        <td class="py-3 px-4 font-mono text-slate-400">{{ $d->transaction_reference }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $d->status === 'approved' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : ($d->status === 'pending' ? 'bg-amber-950 text-amber-400 border border-amber-500/30' : 'bg-red-950 text-red-400 border border-red-500/30') }}">
                                {{ $d->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            @if($d->status === 'pending')
                                <form method="POST" action="{{ route('admin.donations.status', $d->id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="px-2 py-1 rounded bg-emerald-950 hover:bg-emerald-900 text-emerald-300 font-semibold text-[10px] border border-emerald-500/30 mr-1">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.donations.status', $d->id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-2 py-1 rounded bg-red-950 hover:bg-red-900 text-red-300 font-semibold text-[10px] border border-red-500/30">Reject</button>
                                </form>
                            @else
                                <span class="text-slate-500 text-[10px]">Processed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-slate-500">No donation transactions recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-2">
        {{ $donations->links() }}
    </div>
</div>
@endsection

