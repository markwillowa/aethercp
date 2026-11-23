@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Module & Extension Manager</h1>
        <p class="text-xs text-slate-400 mt-1">Enable, disable, and configure control panel modules and extensions.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($modules as $key => $mod)
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $mod['enabled'] ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                            {{ $mod['enabled'] ? 'Active' : 'Disabled' }}
                        </span>
                        <span class="text-xs text-slate-500 font-mono">v{{ $mod['version'] ?? '1.0.0' }}</span>
                    </div>

                    <h3 class="text-base font-bold text-white">{{ $mod['name'] }}</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $mod['description'] }}</p>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500">
                        {{ !empty($mod['protected']) ? 'Core Protected' : ($mod['is_core'] ? 'Core Module' : 'Custom Extension') }}
                    </span>

                    @if(empty($mod['protected']))
                        <form method="POST" action="{{ route('admin.modules.toggle', $key) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $mod['enabled'] ? 'bg-red-950/80 hover:bg-red-900 text-red-300 border border-red-500/30' : 'bg-emerald-950/80 hover:bg-emerald-900 text-emerald-300 border border-emerald-500/30' }}">
                                {{ $mod['enabled'] ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    @else
                        <span class="text-[11px] font-semibold text-slate-500">Locked</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

