@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Theme & Layout Manager</h1>
        <p class="text-xs text-slate-400 mt-1">Switch control panel themes without modifying core codebase.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($themes as $slug => $thm)
            <div class="relative bg-slate-900/80 border {{ $thm['is_active'] ? 'border-cyan-500 shadow-2xl aether-glow-cyan' : 'border-slate-800' }} rounded-2xl p-6 backdrop-blur-md flex flex-col justify-between space-y-4">
                @if($thm['is_active'])
                    <div class="absolute -top-3 right-4 bg-cyan-500 text-slate-950 text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full shadow-md">
                        Active Theme
                    </div>
                @endif

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-cyan-400 font-bold">{{ $slug }}</span>
                        <span class="text-xs text-slate-500 font-mono">v{{ $thm['version'] }}</span>
                    </div>

                    <h3 class="text-base font-bold text-white">{{ $thm['name'] }}</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $thm['description'] }}</p>
                    <p class="text-[11px] text-slate-500">Author: <span class="text-slate-300">{{ $thm['author'] }}</span></p>
                </div>

                <div class="pt-3 border-t border-slate-800">
                    @if($thm['is_active'])
                        <button disabled class="w-full py-2 rounded-xl bg-slate-800 text-slate-400 text-xs font-semibold cursor-not-allowed">
                            Currently Active
                        </button>
                    @elseif(!empty($thm['is_disabled']))
                        <button disabled class="w-full py-2 rounded-xl bg-slate-950 text-slate-600 border border-slate-800 text-xs font-semibold cursor-not-allowed">
                            Disabled
                        </button>
                    @else
                        <form method="POST" action="{{ route('admin.themes.activate', $slug) }}">
                            @csrf
                            <button type="submit" class="w-full py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold transition-colors">
                                Activate Theme
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

