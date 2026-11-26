@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Forum-Style Classic Header Panel -->
    <div class="ro-panel overflow-hidden">
        <div class="ro-panel-header justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                <span>Rune-Midgard Treasury &amp; Donations</span>
            </div>
            @if(!empty($paypalConfig['enabled']) && !empty($paypalConfig['email']))
                <span class="text-[11px] font-mono text-[#FFDA89]">Instant Automated PayPal IPN</span>
            @endif
        </div>

        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-[#FCF7ED]">
            <div class="space-y-1 max-w-2xl">
                <h1 class="text-2xl font-serif font-bold text-[#2D1D13] tracking-tight">
                    Server Donations &amp; Cash Shop Points
                </h1>
                <p class="text-xs text-[#5C4632]">
                    Help sustain dedicated server hosting, DDoS filtering, anti-cheat licenses, and community events.
                    Payments credit Cash Points and deliver bonus items straight into your Kafra Storage.
                </p>
            </div>
            @auth
                <div class="shrink-0 flex items-center gap-2 bg-[#F5EBD5] border border-[#C8A66A] px-3.5 py-2 rounded text-xs text-[#3A2A1C]">
                    <i data-lucide="user-check" class="w-4 h-4 text-[#315B88]"></i>
                    <span>Account: <strong>{{ auth()->user()->username ?? auth()->user()->name }}</strong></span>
                </div>
            @endauth
        </div>

        @if(!empty($donationSettings['instructions']))
            <div class="px-5 py-3 border-t border-[#DECBB1] text-xs text-[#5C4228] leading-relaxed flex items-start gap-2.5 bg-[#F5EBD5]">
                <i data-lucide="scroll" class="w-4 h-4 text-[#8C6A2D] shrink-0 mt-0.5"></i>
                <div>
                    <strong class="block text-[#3A2A1C] font-serif font-bold mb-0.5">Donation Notice &amp; Guidelines:</strong>
                    <p class="whitespace-pre-line">{{ $donationSettings['instructions'] }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Package Tiers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($packages as $pkg)
            <div class="ro-panel relative flex flex-col justify-between p-0 overflow-hidden {{ $pkg->is_popular ? 'border-2 border-[#B89553] shadow-xl' : 'border border-[#DECBB1]' }}">
                @if($pkg->is_popular)
                    <div class="bg-gradient-to-r from-[#B89553] to-[#8C6A2D] text-[#FCF7ED] text-[10px] font-bold uppercase tracking-widest text-center py-1 font-serif shadow-sm">
                        ★ Most Favored by Adventurers ★
                    </div>
                @endif

                <div class="p-6 space-y-5">
                    <div>
                        <h3 class="text-lg font-bold font-serif text-[#2D1D13]">{{ $pkg->name }}</h3>
                        <p class="text-xs text-[#77644E] mt-1">{{ $pkg->description }}</p>
                    </div>

                    <div class="bg-[#F5EBD5] border border-[#DECBB1] rounded p-4 text-center">
                        <span class="text-3xl font-extrabold font-serif text-[#315B88]">${{ number_format($pkg->price, 2) }}</span>
                        <span class="text-xs font-bold text-[#77644E] uppercase ml-1">{{ $pkg->currency }}</span>
                    </div>

                    <div class="space-y-2 text-xs divide-y divide-[#DECBB1]/60">
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[#77644E] font-medium">Cash Points:</span>
                            <strong class="text-[#2E7D32] font-bold text-sm">{{ number_format($pkg->points) }} pts</strong>
                        </div>

                        @if($pkg->bonus_points > 0)
                            <div class="flex items-center justify-between pt-2 text-[#8C6A2D]">
                                <span class="font-medium">Bonus Points:</span>
                                <strong class="font-bold">+{{ number_format($pkg->bonus_points) }} pts</strong>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-2 text-[#3A2A1C] font-bold">
                            <span>Total Credited:</span>
                            <strong class="text-[#315B88] text-sm">{{ number_format($pkg->points + $pkg->bonus_points) }} Points</strong>
                        </div>

                        @if($pkg->item_amount > 0 && ($pkg->item_name || $pkg->item_id))
                            <div class="pt-3">
                                <div class="bg-[#FCF4E0] border border-[#C8A66A] rounded p-2.5 flex items-start gap-2.5 text-[#5C4228]">
                                    <i data-lucide="gift" class="w-4 h-4 text-[#8C6A2D] shrink-0 mt-0.5"></i>
                                    <div class="text-[11px]">
                                        <div class="font-bold text-[#493322]">
                                            +{{ $pkg->item_amount }}x {{ $pkg->item_name ?: 'Reward Item #' . $pkg->item_id }}
                                        </div>
                                        <div class="text-[10px] text-[#77644E]">
                                            Delivered directly to Kafra Storage
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-6 pt-0 space-y-2">
                    @auth
                        @if(!empty($paypalConfig['enabled']) && !empty($paypalConfig['email']))
                            <form method="POST" action="{{ route('donations.paypal.checkout', $pkg->id) }}" class="w-full">
                                @csrf
                                <button type="submit" class="ro-btn-gold w-full py-2.5 text-xs flex items-center justify-center gap-2 cursor-pointer shadow-md">
                                    <i data-lucide="credit-card" class="w-4 h-4 text-[#493322]"></i>
                                    <span>Instant PayPal Checkout</span>
                                </button>
                            </form>
                            <button type="button" onclick="selectAndScrollClassicPackage({{ $pkg->id }})"
                                class="ro-btn-wood w-full py-1.5 text-[11px] flex items-center justify-center gap-1.5 cursor-pointer">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>Manual Reference Claim</span>
                            </button>
                        @else
                            <button type="button" onclick="selectAndScrollClassicPackage({{ $pkg->id }})"
                                class="ro-btn-primary w-full py-2.5 text-xs flex items-center justify-center gap-2 cursor-pointer">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Select Tier for Manual Claim</span>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="ro-btn-wood w-full py-2.5 text-xs text-center block">
                            Sign In to Donate
                        </a>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    @auth
        <!-- Manual Donation Claim Form -->
        <div id="manual-claim-card" class="ro-panel max-w-2xl mx-auto p-0 overflow-hidden shadow-xl border border-[#DECBB1]">
            <div class="ro-panel-header flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4 text-[#FFDA89]"></i>
                <span>Manual Donation Claim & Staff Verification</span>
            </div>

            <div class="p-6 bg-[#FCF7ED] space-y-4">
                <p class="text-xs text-[#77644E]">
                    If you contributed using alternative payment channels (Bank Transfer, G-Cash, Crypto) or made an offline PayPal transfer,
                    please record your transaction reference number below for Game Master audit and crediting.
                </p>

                @if(!empty($donationSettings['payment_details']))
                    <div class="bg-[#F5EBD5] border border-[#C8A66A] p-3 rounded text-xs text-[#5C4228] space-y-1">
                        <strong class="font-serif font-bold text-[#3A2A1C] flex items-center gap-1.5">
                            <i data-lucide="wallet" class="w-3.5 h-3.5 text-[#8C6A2D]"></i>
                            <span>Manual Payment Instructions &amp; Bank / E-Wallet Info:</span>
                        </strong>
                        <div class="whitespace-pre-line text-[11px] leading-relaxed">{{ $donationSettings['payment_details'] }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('donations.submit') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold font-serif text-[#3A2A1C] uppercase tracking-wider mb-1">
                            Donation Tier
                        </label>
                        <select id="classic_package_select" name="package_id" class="w-full px-3 py-2 bg-[#F5EBD5] border border-[#DECBB1] rounded text-sm text-[#3A2A1C] focus:border-[#315B88] focus:outline-none">
                            @foreach($packages as $pkg)
                                <option id="classic_pkg_{{ $pkg->id }}" value="{{ $pkg->id }}">
                                    {{ $pkg->name }} — ${{ number_format($pkg->price, 2) }} ({{ number_format($pkg->points + $pkg->bonus_points) }} Points{{ $pkg->item_amount > 0 ? ' + ' . $pkg->item_amount . 'x ' . ($pkg->item_name ?: 'Bonus Item') : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold font-serif text-[#3A2A1C] uppercase tracking-wider mb-1">
                            Transaction Reference / Receipt ID
                        </label>
                        <input type="text" name="transaction_reference" required placeholder="e.g. TXN-1094829 or PayPal Transaction ID"
                            class="w-full px-3 py-2 bg-[#F5EBD5] border border-[#DECBB1] rounded text-sm text-[#3A2A1C] placeholder-[#9C856C] focus:border-[#315B88] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold font-serif text-[#3A2A1C] uppercase tracking-wider mb-1">
                            Additional Notes
                        </label>
                        <textarea name="notes" rows="2" placeholder="Sender name, method of payment, or transaction timestamp..."
                            class="w-full px-3 py-2 bg-[#F5EBD5] border border-[#DECBB1] rounded text-xs text-[#3A2A1C] placeholder-[#9C856C] focus:border-[#315B88] focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="ro-btn-primary w-full py-2.5 text-xs flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Submit for GM Verification</span>
                    </button>
                </form>
            </div>
        </div>

        @if(count($userDonations) > 0)
            <!-- User Donation History -->
            <div class="ro-panel p-0 overflow-hidden shadow-xl border border-[#DECBB1]">
                <div class="ro-panel-header flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-[#FFDA89]"></i>
                        <span>My Account Donation Ledger</span>
                    </div>
                    <span class="text-xs text-[#FFDA89] font-normal font-sans">{{ count($userDonations) }} records recorded</span>
                </div>

                <div class="overflow-x-auto bg-[#FCF7ED]">
                    <table class="ro-table w-full text-left text-xs">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Package Tier</th>
                                <th>Amount</th>
                                <th>Cash Points</th>
                                <th>Bonus Storage Items</th>
                                <th>Method / Reference</th>
                                <th class="text-center">Audit Status</th>
                                <th class="text-center">In-Game Delivery</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userDonations as $d)
                                <tr>
                                    <td class="text-[#77644E]">{{ $d->created_at->format('M d, Y') }}</td>
                                    <td class="font-bold text-[#3A2A1C]">{{ $d->package?->name ?? 'Custom Tier' }}</td>
                                    <td class="font-bold text-[#315B88]">${{ number_format($d->amount, 2) }}</td>
                                    <td class="font-bold text-[#2E7D32]">{{ number_format($d->points_awarded) }}</td>
                                    <td>
                                        @if($d->package && $d->package->item_amount > 0)
                                            <span class="text-[#8C6A2D] font-bold">{{ $d->package->item_amount }}x {{ $d->package->item_name ?: 'Item #'.$d->package->item_id }}</span>
                                        @else
                                            <span class="text-[#A89885]">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-mono text-xs text-[#3A2A1C]">{{ $d->transaction_reference }}</div>
                                        <div class="text-[10px] text-[#77644E] uppercase">{{ $d->payment_method }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($d->status === 'approved')
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#D1E7DD] text-[#0F5132] border border-[#BADBCC]">
                                                Approved
                                            </span>
                                        @elseif($d->status === 'pending')
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#FFF3CD] text-[#664D03] border border-[#FFECB5]">
                                                Pending Audit
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#F8D7DA] text-[#842029] border border-[#F5C2C7]">
                                                Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($d->status === 'approved')
                                            <div class="flex flex-col items-center gap-0.5 text-[10px]">
                                                @if($d->points_delivered)
                                                    <span class="text-[#2E7D32] font-semibold flex items-center gap-1">
                                                        <i data-lucide="check" class="w-3 h-3"></i> Points Credited
                                                    </span>
                                                @else
                                                    <span class="text-[#B89553] font-semibold flex items-center gap-1">
                                                        <i data-lucide="clock" class="w-3 h-3"></i> Points Queued
                                                    </span>
                                                @endif

                                                @if($d->package && $d->package->item_amount > 0)
                                                    @if($d->items_delivered)
                                                        <span class="text-[#2E7D32] font-semibold flex items-center gap-1">
                                                            <i data-lucide="package-check" class="w-3 h-3"></i> Storage Item Delivered
                                                        </span>
                                                    @else
                                                        <span class="text-[#B89553] font-semibold flex items-center gap-1">
                                                            <i data-lucide="clock" class="w-3 h-3"></i> Storage Item Queued
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-[#77644E] text-[11px]">Awaiting Verification</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endauth

    @if(!empty($donationSettings['kafra_guide']))
        <div class="ro-panel p-5 bg-[#FCF7ED] border border-[#DECBB1] shadow-md space-y-2">
            <div class="flex items-center gap-2">
                <i data-lucide="package-check" class="w-4 h-4 text-[#2E7D32]"></i>
                <h3 class="text-xs font-bold font-serif uppercase tracking-wider text-[#3A2A1C]">Kafra Storage Delivery &amp; Reward Instructions</h3>
            </div>
            <p class="text-xs text-[#5C4228] whitespace-pre-line leading-relaxed">{{ $donationSettings['kafra_guide'] }}</p>
        </div>
    @endif

    @if(!empty($donationSettings['terms']))
        <div class="p-4 bg-[#F5EBD5] border border-[#DECBB1] rounded text-[11px] text-[#77644E] space-y-1">
            <strong class="font-serif font-bold text-[#3A2A1C] uppercase tracking-wider text-[10px] block">Rune-Midgard Support Terms &amp; Policy:</strong>
            <p class="whitespace-pre-line leading-relaxed">{{ $donationSettings['terms'] }}</p>
        </div>
    @endif
</div>

<script>
    function selectAndScrollClassicPackage(pkgId) {
        var opt = document.getElementById('classic_pkg_' + pkgId);
        if (opt) {
            opt.selected = true;
        }
        var target = document.getElementById('manual-claim-card');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
@endsection

