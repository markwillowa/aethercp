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

        <button type="submit" class="py-2.5 px-6 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-md transition-colors">
            Save Site Settings
        </button>
    </form>
</div>
@endsection

