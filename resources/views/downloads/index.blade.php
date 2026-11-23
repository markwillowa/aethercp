@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Client Downloads & Installation</h1>
        <p class="text-xs text-slate-400 mt-1">Download official game packages and cumulative patches.</p>
    </div>

    <!-- Download Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($downloads as $dl)
            <div class="bg-slate-900/80 border border-slate-800 hover:border-slate-700/80 rounded-2xl p-6 backdrop-blur-md shadow-xl transition-all duration-200 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                            {{ $dl->category }}
                        </span>
                        <span class="text-xs font-mono text-slate-400">{{ $dl->file_size }}</span>
                    </div>

                    <h3 class="text-lg font-bold text-white">{{ $dl->title }}</h3>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ $dl->description }}
                    </p>

                    @if($dl->version)
                        <p class="text-[11px] text-slate-500">Version: <span class="text-slate-300 font-medium">{{ $dl->version }}</span></p>
                    @endif
                </div>

                <!-- Mirror Buttons -->
                <div class="space-y-2 pt-4 border-t border-slate-800">
                    <a href="{{ $dl->url_mirror1 }}" target="_blank" rel="noopener"
                       class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Download Mirror 1</span>
                    </a>

                    @if($dl->url_mirror2)
                        <a href="{{ $dl->url_mirror2 }}" target="_blank" rel="noopener"
                           class="w-full py-2 px-4 rounded-xl bg-slate-950 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 font-medium text-xs transition-colors flex items-center justify-center gap-2">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Alternative Mirror 2</span>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Installation Instructions Guide -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 sm:p-8 backdrop-blur-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
            <i data-lucide="help-circle" class="w-5 h-5 text-cyan-400"></i>
            <h2 class="text-base font-bold text-white">Installation Guide</h2>
        </div>
        <ol class="list-decimal list-inside space-y-2 text-xs sm:text-sm text-slate-300 leading-relaxed">
            <li>Download either the <strong>Full Client</strong> (self-contained) or <strong>Lite Client</strong> (if you already have clean kRO).</li>
            <li>Extract the contents using WinRAR or 7-Zip into your preferred installation folder (e.g. <code class="text-cyan-400">C:\Games\Ragnarok\</code>).</li>
            <li>Run <code class="text-cyan-400">Patcher.exe</code> as Administrator to fetch any remaining patch files.</li>
            <li>Launch <code class="text-cyan-400">Setup.exe</code> to configure your display resolution and sound settings.</li>
            <li>Start your adventure with <code class="text-cyan-400">Play.exe</code> and log in with your registered account!</li>
        </ol>
    </div>
</div>
@endsection

