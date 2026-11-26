@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">Promotional Rewards</span>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-1">Redeem Code</h1>
            <p class="text-xs text-slate-400 mt-1">Enter a promotional or event code to preview rewards and claim them to your account.</p>
        </div>
        <a href="{{ route('account.profile') }}" class="text-xs text-slate-400 hover:text-white">Back to Account</a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs">{{ session('error') }}</div>
    @endif

    <!-- Step 1: Enter Code & Preview -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 space-y-5">
        <form method="POST" action="{{ route('account.redeem.preview') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Redeem Code</label>
                <input type="text" name="code" value="{{ old('code', session('redeem_preview.code', '')) }}" required placeholder="e.g. WELCOME-2027"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm font-mono uppercase text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Target Character (If Required)</label>
                <select name="character_id" class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white">
                    <option value="">Account-wide / Select Character</option>
                    @foreach($characters as $c)
                        <option value="{{ $c->char_id }}" {{ (int) old('character_id', session('redeem_preview.character_id')) === (int) $c->char_id ? 'selected' : '' }}>
                            {{ $c->name }} (Lv. {{ $c->base_level }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="py-2.5 px-5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-sm font-bold cursor-pointer">
                Validate &amp; Preview Rewards
            </button>
        </form>

        <!-- Step 2: Reward Preview & Confirmation -->
        @if(session('redeem_preview'))
            @php $pv = session('redeem_preview'); @endphp
            <div class="p-5 rounded-2xl bg-slate-950/70 border border-emerald-500/40 space-y-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Code Validated</span>
                    <h2 class="text-lg font-bold text-white">{{ $pv['campaign_name'] }}</h2>
                    @if(!empty($pv['campaign_description']))
                        <p class="text-xs text-slate-400 mt-1">{{ $pv['campaign_description'] }}</p>
                    @endif
                </div>
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-300">Rewards Included:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pv['rewards'] as $rw)
                            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-cyan-300">{{ $rw['label'] }}</span>
                        @endforeach
                    </div>
                </div>
                <form method="POST" action="{{ route('account.redeem.store') }}">
                    @csrf
                    <input type="hidden" name="code" value="{{ $pv['code'] }}">
                    <input type="hidden" name="character_id" value="{{ $pv['character_id'] }}">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-950 text-sm font-bold cursor-pointer">
                        Confirm &amp; Claim Rewards
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Redemption History -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden">
        <div class="p-4 border-b border-slate-800">
            <h2 class="text-sm font-bold text-white">My Redemption History</h2>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                <tr>
                    <th class="py-3 px-4">Code</th>
                    <th class="py-3 px-4">Campaign</th>
                    <th class="py-3 px-4">Rewards</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($history as $h)
                    <tr>
                        <td class="py-3 px-4 font-mono text-cyan-300">{{ $h->code_hint }}</td>
                        <td class="py-3 px-4 text-white">{{ $h->campaign?->name }}</td>
                        <td class="py-3 px-4 text-slate-300">{{ implode(', ', $h->reward_summary ?? []) }}</td>
                        <td class="py-3 px-4 uppercase font-bold text-[10px]">{{ $h->status }}</td>
                        <td class="py-3 px-4 text-slate-400">{{ $h->redeemed_at?->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-slate-500">You haven't redeemed any codes yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $history->links() }}</div>
</div>
@endsection
