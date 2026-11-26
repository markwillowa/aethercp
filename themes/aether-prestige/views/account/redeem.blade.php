@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <!-- News & Marketplace Style Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Redeem Code
            </h1>
            <p class="text-xs text-[#788394] mt-1">
                Enter a promotional or event code to preview rewards and claim them to your account.
            </p>
        </div>
        <a href="{{ route('account.profile') }}" class="h-9 px-4 rounded-xl bg-[#151B26] border border-white/[0.08] text-xs font-bold text-[#AAB3C1] hover:text-white inline-flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4 text-[#C6A76A]"></i>
            <span>Back to Account</span>
        </a>
    </div>

    <!-- Step 1: Enter Code & Preview -->
    <div class="prestige-card p-6 space-y-5">
        <form method="POST" action="{{ route('account.redeem.preview') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-[#D9DEE7] mb-1.5">Redeem Code</label>
                <input type="text" name="code" value="{{ old('code', session('redeem_preview.code', '')) }}" required placeholder="e.g. WELCOME-2027"
                       class="w-full h-10 px-4 rounded-xl bg-[#101725] border border-white/10 text-sm font-mono uppercase text-white focus:border-[#C6A76A] focus:outline-none">
            </div>
            <button type="submit" class="h-10 px-5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel text-xs font-bold uppercase tracking-wider cursor-pointer">
                Validate &amp; Preview Rewards
            </button>
        </form>
        <p class="text-[11px] text-[#788394]">
            Items and Zeny from redeemed codes are delivered safely to your <a href="{{ route('escrow.index') }}" class="text-[#D8BE8A] hover:underline font-semibold">Escrow Inbox</a>, where you can choose which character receives them.
        </p>

        @if(session('redeem_preview'))
            @php $pv = session('redeem_preview'); @endphp
            <div class="p-5 rounded-2xl bg-[#101725] border border-[#C6A76A]/40 space-y-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#D8BE8A]">Code Validated</span>
                    <h2 class="font-cinzel text-lg font-bold text-white">{{ $pv['campaign_name'] }}</h2>
                    @if(!empty($pv['campaign_description']))
                        <p class="text-xs text-[#AAB3C1] mt-1">{{ $pv['campaign_description'] }}</p>
                    @endif
                </div>
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-[#D9DEE7]">Rewards Included:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pv['rewards'] as $rw)
                            <span class="px-3 py-1.5 rounded-xl bg-[#151B26] border border-[#C6A76A]/30 text-xs font-bold text-[#D8BE8A]">{{ $rw['label'] }}</span>
                        @endforeach
                    </div>
                </div>
                <form method="POST" action="{{ route('account.redeem.store') }}">
                    @csrf
                    <input type="hidden" name="code" value="{{ $pv['code'] }}">
                    <input type="hidden" name="character_id" value="{{ $pv['character_id'] }}">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel text-xs font-bold uppercase tracking-wider cursor-pointer">
                        Confirm &amp; Claim Rewards
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Redemption History -->
    <div class="prestige-card overflow-hidden">
        <div class="p-5 border-b border-white/[0.08]">
            <h2 class="font-cinzel text-sm font-bold text-white uppercase tracking-wider">My Redemption History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#101725] text-[#788394] font-cinzel uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Campaign</th>
                        <th class="py-3 px-4">Rewards</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    @forelse($history as $h)
                        <tr class="hover:bg-white/[0.02]">
                            <td class="py-3 px-4 font-mono text-[#D8BE8A]">{{ $h->code_hint }}</td>
                            <td class="py-3 px-4 text-white font-semibold">{{ $h->campaign?->name }}</td>
                            <td class="py-3 px-4 text-[#D9DEE7]">{{ implode(', ', $h->reward_summary ?? []) }}</td>
                            <td class="py-3 px-4 uppercase font-bold text-[10px] text-emerald-400">{{ $h->status }}</td>
                            <td class="py-3 px-4 text-[#788394]">{{ $h->redeemed_at?->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-[#788394]">You haven't redeemed any codes yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>{{ $history->links() }}</div>
</div>
@endsection
