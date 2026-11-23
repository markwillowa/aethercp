@extends('layouts.admin')

@section('admin_content')
<div class="max-w-3xl space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Server & Site Settings</h1>
        <p class="text-xs text-slate-400 mt-1">Configure live server rates, server identity, and community URLs.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Server Name</label>
                <input type="text" name="server_name" value="{{ $settings['server_name'] ?? 'Freynix RO' }}"
                    class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Tagline</label>
                <input type="text" name="tagline" value="{{ $settings['tagline'] ?? '' }}"
                    class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Base Rate</label>
                <input type="text" name="base_rate" value="{{ $settings['base_rate'] ?? '1000x' }}"
                    class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Job Rate</label>
                <input type="text" name="job_rate" value="{{ $settings['job_rate'] ?? '1000x' }}"
                    class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Drop Rate</label>
                <input type="text" name="drop_rate" value="{{ $settings['drop_rate'] ?? '100x' }}"
                    class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Discord Invite URL</label>
                <input type="url" name="discord_url" value="{{ $settings['discord_url'] ?? '' }}"
                    class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Client Version</label>
                <input type="text" name="client_version" value="{{ $settings['client_version'] ?? '2023-11-01' }}"
                    class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            </div>
        </div>

        <!-- PayPal Automated Donations & In-Game Rewards Configuration -->
        <div class="pt-4 border-t border-slate-800 space-y-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">PayPal Gateway & Automated In-Game Delivery</h3>
                    <p class="text-[11px] text-slate-400">Automate cash points (#CASHPOINTS) and storage item rewards when players pay via PayPal.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">PayPal Merchant Email</label>
                    <input type="email" name="paypal_email" value="{{ $settings['paypal_email'] ?? '' }}"
                        placeholder="your-paypal-business@example.com"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    <p class="text-[10px] text-slate-500 mt-1">Payments made on the donation page will be sent directly to this address.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">PayPal Gateway Status</label>
                    <select name="paypal_enabled" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        <option value="1" {{ ($settings['paypal_enabled'] ?? '1') == '1' ? 'selected' : '' }}>Enabled (Accept PayPal Payments)</option>
                        <option value="0" {{ ($settings['paypal_enabled'] ?? '1') == '0' ? 'selected' : '' }}>Disabled (Manual Payments Only)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Environment Mode</label>
                    <select name="paypal_sandbox" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        <option value="1" {{ ($settings['paypal_sandbox'] ?? '1') == '1' ? 'selected' : '' }}>Sandbox Mode (Testing / Simulation)</option>
                        <option value="0" {{ ($settings['paypal_sandbox'] ?? '1') == '0' ? 'selected' : '' }}>Live Production (Real Money)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Currency Code</label>
                    <input type="text" name="paypal_currency" value="{{ $settings['paypal_currency'] ?? 'USD' }}"
                        placeholder="USD" maxlength="3"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 uppercase">
                </div>
            </div>

            <div class="p-3 bg-slate-950/70 border border-slate-800 rounded-xl text-xs text-slate-400 space-y-1">
                <span class="font-bold text-cyan-400">Instant Payment Notification (IPN) Webhook URL:</span>
                <p class="font-mono text-[11px] text-slate-300 break-all select-all">{{ url('/donations/paypal/ipn') }}</p>
                <p class="text-[10px] text-slate-500 pt-1">Configure this URL in your PayPal Account Settings &gt; Instant Payment Notification to automate reward delivery without staff intervention.</p>
            </div>
        </div>

        <button type="submit" class="py-2.5 px-6 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-md transition-colors">
            Save Site Settings
        </button>
    </form>
</div>
@endsection

