@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">
    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#D4AF37]/20 text-[#FFE4A0] border border-[#D4AF37]/40">Promotional Rewards</span>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase mt-1">Redeem Code</h1>
            <p class="text-xs text-[#EAD2B0] mt-1">Enter a promotional or event code to preview rewards and claim them to your Escrow Inbox.</p>
        </div>
        <a href="{{ route('account.profile') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>Account Profile</span>
        </a>
    </div>

    <!-- Step 1: Enter Code & Preview -->
    <div class="chronicles-panel-ivy space-y-5">
        <form method="POST" action="{{ route('account.redeem.preview') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-xs font-cinzel font-bold text-[#4A2200] mb-1.5">Redeem Code</label>
                <input type="text" name="code" value="{{ old('code', session('redeem_preview.code', '')) }}" required placeholder="e.g. WELCOME-2027"
                       class="w-full px-4 py-2.5 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] text-sm font-mono uppercase text-[#4A2200] focus:border-[#9E5600] focus:outline-none">
            </div>
            <button type="submit" class="py-2.5 px-5 rounded-xl bg-[#4A2200] hover:bg-[#5E2C00] text-[#FFE4A0] border border-[#8B5A2B] text-xs font-cinzel font-bold cursor-pointer transition-colors">
                Validate &amp; Preview Rewards
            </button>
        </form>
        <p class="text-[11px] text-[#795E43]">
            Items and Zeny from redeemed codes are delivered safely to your <a href="{{ route('escrow.index') }}" class="text-[#D4441D] hover:underline font-bold">Escrow Inbox</a>, where you can choose which character receives them.
        </p>

        @if(session('redeem_preview'))
            @php $pv = session('redeem_preview'); @endphp
            <div class="p-5 rounded-2xl bg-[#FAF4EA] border-2 border-[#4CAF50]/60 space-y-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#1E4620]">Code Validated</span>
                    <h2 class="font-cinzel text-lg font-black text-[#4A2200]">{{ $pv['campaign_name'] }}</h2>
                    @if(!empty($pv['campaign_description']))
                        <p class="text-xs text-[#795E43] mt-1">{{ $pv['campaign_description'] }}</p>
                    @endif
                </div>
                <div class="space-y-2">
                    <p class="text-xs font-bold text-[#4A2200]">Rewards Included:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pv['rewards'] as $rw)
                            <span class="px-3 py-1.5 rounded-xl bg-[#EFE4D0] border border-[#DECBB1] text-xs font-bold text-[#9E5600]">{{ $rw['label'] }}</span>
                        @endforeach
                    </div>
                </div>
                <form method="POST" action="{{ route('account.redeem.store') }}">
                    @csrf
                    <input type="hidden" name="code" value="{{ $pv['code'] }}">
                    <input type="hidden" name="character_id" value="{{ $pv['character_id'] }}">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1E4620] hover:bg-[#275A2A] text-[#D8FFB8] border border-[#4CAF50] text-xs font-cinzel font-bold cursor-pointer">
                        Confirm &amp; Claim Rewards
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Redemption History -->
    <div class="chronicles-panel shadow-2xl overflow-hidden">
        <div class="chronicles-panel-header px-5 py-3">
            <span class="chronicles-panel-title">
                <i data-lucide="history" class="w-4 h-4"></i>
                <span>My Redemption History</span>
            </span>
        </div>
        <div class="p-4 sm:p-6 overflow-x-auto">
            <table class="chronicles-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Campaign</th>
                        <th>Rewards</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $h)
                        <tr>
                            <td class="font-mono text-[#FFD700]">{{ $h->code_hint }}</td>
                            <td class="font-bold text-[#F3E5AB]">{{ $h->campaign?->name }}</td>
                            <td class="text-[#EDE4D2]">{{ implode(', ', $h->reward_summary ?? []) }}</td>
                            <td class="uppercase font-bold text-[10px]">{{ $h->status }}</td>
                            <td class="text-[#A396AD]">{{ $h->redeemed_at?->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-[#8C7F99]">You haven't redeemed any codes yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>{{ $history->links() }}</div>
</div>
@endsection
