@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Client Downloads & Mirrors
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Install official game packages, custom sound packs, and high-resolution patches
            </p>
        </div>
        <div class="chronicles-status-pill online text-xs py-1.5 px-3">
            <span>High Speed Direct CDN</span>
        </div>
    </div>

    <!-- Downloads Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($downloads as $dl)
            <div class="chronicles-panel-ivy flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#FFD700] text-[#3B1F0A] border border-[#B8860B] font-cinzel">
                            {{ $dl->category }}
                        </span>
                        <span class="text-xs font-mono text-[#8C7A68] font-bold">{{ $dl->file_size }}</span>
                    </div>

                    <h3 class="font-cinzel text-lg font-bold text-[#4A2200]">{{ $dl->title }}</h3>

                    <p class="text-xs text-[#5A3D28] leading-relaxed">
                        {{ $dl->description }}
                    </p>

                    @if($dl->version)
                        <p class="text-[11px] text-[#8C7A68]">Build Version: <strong class="text-[#4A2200] font-mono">{{ $dl->version }}</strong></p>
                    @endif
                </div>

                <!-- Mirror Buttons -->
                <div class="space-y-2 pt-4 border-t border-[#DECBB1]">
                    <a href="{{ $dl->url_mirror1 }}" target="_blank" rel="noopener"
                       class="chronicles-btn-red w-full text-xs py-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Primary Mirror</span>
                    </a>

                    @if($dl->url_mirror2)
                        <a href="{{ $dl->url_mirror2 }}" target="_blank" rel="noopener"
                           class="chronicles-btn-wood text-xs py-2">
                            <i data-lucide="external-link" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                            <span>Secondary Mirror</span>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <!-- Fallback Default Classic RO Download Cards -->
            <div class="chronicles-panel-ivy flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#FFD700] text-[#3B1F0A] font-cinzel">FULL CLIENT</span>
                        <span class="text-xs font-mono text-[#8C7A68] font-bold">1.85 GB</span>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-[#4A2200]">Complete Game Installer</h3>
                    <p class="text-xs text-[#5A3D28]">Includes full BGM audio, sprite data, Gepard Anti-Cheat, and patch updater. Ready to play after install.</p>
                </div>
                <div class="space-y-2 pt-4 border-t border-[#DECBB1]">
                    <a href="#" class="chronicles-btn-red w-full text-xs py-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Google Drive Mirror</span>
                    </a>
                    <a href="#" class="chronicles-btn-wood text-xs py-2">
                        <i data-lucide="download" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                        <span>Mega.nz Mirror</span>
                    </a>
                </div>
            </div>

            <div class="chronicles-panel-ivy flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#2E7D32] text-white font-cinzel">LITE CLIENT</span>
                        <span class="text-xs font-mono text-[#8C7A68] font-bold">185 MB</span>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-[#4A2200]">Small Patch Package</h3>
                    <p class="text-xs text-[#5A3D28]">Recommended for veterans who already have a clean kRO/bRO data directory. Extract into existing RO folder.</p>
                </div>
                <div class="space-y-2 pt-4 border-t border-[#DECBB1]">
                    <a href="#" class="chronicles-btn-red w-full text-xs py-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>MediaFire Mirror</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Specifications Table Panel -->
    <div class="chronicles-panel-ivy pt-6">
        <h3 class="font-cinzel text-base font-bold text-[#4A2200] uppercase mb-3">System Specifications</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="bg-[#FAF4EA] p-4 rounded-xl border border-[#DECBB1] space-y-1.5">
                <strong class="font-cinzel text-[#9E5600] block text-sm">Minimum Requirements</strong>
                <p>• OS: Windows 7 / 8 / 10 / 11</p>
                <p>• Processor: Intel Pentium 4 2.0 GHz or AMD equivalent</p>
                <p>• RAM: 2 GB Memory</p>
                <p>• Storage: 4 GB available storage</p>
            </div>
            <div class="bg-[#FAF4EA] p-4 rounded-xl border border-[#DECBB1] space-y-1.5">
                <strong class="font-cinzel text-[#2D5A1E] block text-sm">Recommended Configuration</strong>
                <p>• OS: Windows 10 / 11 64-bit</p>
                <p>• Processor: Intel Core i3 / Ryzen 3 or higher</p>
                <p>• RAM: 4 GB+ Memory</p>
                <p>• Storage: Solid State Drive (SSD) for lightning load times</p>
            </div>
        </div>
    </div>

</div>
@endsection
