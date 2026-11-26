@extends('layouts.app')

@section('content')
<div class="payon-container py-10 space-y-8">
    <div class="payon-wood-frame overflow-hidden">
        <div class="payon-wood-header">
            <div class="flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4 text-[#A88950]"></i>
                <span class="font-cinzel uppercase tracking-wider text-sm">Client &amp; Patch Scrolls</span>
            </div>
            <span class="text-xs text-[#E7D8B5]">Official Game Packages</span>
        </div>
        <div class="p-6">
            <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">Client Downloads &amp; Installation</h1>
            <p class="text-xs text-[#80633B] mt-1">Download official game packages, cumulative patches, and setup scrolls.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($downloads as $dl)
            <div class="payon-scroll-panel p-6 flex flex-col justify-between space-y-5">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-[#263D2C] text-[#FFF8E8]">{{ $dl->category }}</span>
                        <span class="text-xs font-mono text-[#80633B]">{{ $dl->file_size }}</span>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-[#35291F]">{{ $dl->title }}</h3>
                    <p class="text-xs text-[#493322] leading-relaxed">{{ $dl->description }}</p>
                    @if($dl->version)
                        <p class="text-[11px] font-mono text-[#80633B]">Version: <strong class="text-[#35291F]">{{ $dl->version }}</strong></p>
                    @endif
                </div>
                <div class="space-y-2 pt-4 border-t border-[#A88950]/40">
                    <a href="{{ $dl->url_mirror1 }}" target="_blank" rel="noopener" class="payon-btn-forest w-full py-2.5">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Download Mirror 1</span>
                    </a>
                    @if($dl->url_mirror2)
                        <a href="{{ $dl->url_mirror2 }}" target="_blank" rel="noopener" class="payon-btn-gold w-full py-2">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Alternative Mirror 2</span>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="payon-scroll-panel p-6 sm:p-8 space-y-4">
        <div class="flex items-center gap-2 border-b border-[#A88950]/40 pb-3">
            <i data-lucide="book-open" class="w-5 h-5 text-[#526D3A]"></i>
            <h2 class="font-cinzel text-base font-bold text-[#35291F]">Installation Guide Scroll</h2>
        </div>
        <ol class="list-decimal list-inside space-y-2 text-xs sm:text-sm text-[#493322] leading-relaxed">
            <li>Download either the <strong>Full Client</strong> (self-contained) or <strong>Lite Client</strong> (if you already have clean kRO).</li>
            <li>Extract the archive to a clean directory outside Program Files (e.g. <code class="font-mono text-[#263D2C]">C:\Games\PayonRO</code>).</li>
            <li>Run the patcher as Administrator to apply the latest kingdom updates, then launch the game!</li>
        </ol>
    </div>
</div>
@endsection
