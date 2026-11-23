@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Character Records</h1>
            <p class="text-xs text-slate-400 mt-1">Search and view character data across all accounts.</p>
        </div>

        <form method="GET" action="{{ route('admin.characters') }}" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search character name or ID..."
                class="px-3 py-1.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            <button type="submit" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl text-xs">Search</button>
        </form>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                    <tr>
                        <th class="py-3 px-4">Char ID</th>
                        <th class="py-3 px-4">Account ID</th>
                        <th class="py-3 px-4">Character Name</th>
                        <th class="py-3 px-4 text-center">Class ID</th>
                        <th class="py-3 px-4 text-center">Base / Job</th>
                        <th class="py-3 px-4 text-right">Zeny</th>
                        <th class="py-3 px-4">Map Location</th>
                        <th class="py-3 px-4 text-center">Online</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($characters as $c)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400">#{{ $c->char_id }}</td>
                            <td class="py-3 px-4 font-mono text-slate-400">#{{ $c->account_id }}</td>
                            <td class="py-3 px-4 font-bold text-white">{{ $c->name }}</td>
                            <td class="py-3 px-4 text-center font-mono">{{ $c->class }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-200">{{ $c->base_level }} / {{ $c->job_level }}</td>
                            <td class="py-3 px-4 text-right font-mono text-amber-400">{{ number_format($c->zeny) }} Z</td>
                            <td class="py-3 px-4 text-slate-300">{{ $c->last_map }} ({{ $c->last_x }}, {{ $c->last_y }})</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $c->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                                    {{ $c->online > 0 ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-slate-500">No character records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        {{ $characters->links() }}
    </div>
</div>
@endsection

