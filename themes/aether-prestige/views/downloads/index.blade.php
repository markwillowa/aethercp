@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-10">
    <div class="border-b border-white/[0.08] pb-6">
        <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
            Client Downloads & Mirrors
        </h1>
        <p class="text-xs text-[#788394] mt-1">Download official high-definition game installers, lite packages, and cumulative patch files.</p>
    </div>

    <!-- Download Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($downloads as $dl)
            <div class="prestige-card p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40 font-cinzel">
                            {{ $dl->category }}
                        </span>
                        <span class="text-xs font-mono text-[#788394]">{{ $dl->file_size }}</span>
                    </div>

                    <h3 class="font-cinzel text-lg font-bold text-white">{{ $dl->title }}</h3>

                    <p class="text-xs text-[#AAB3C1] leading-relaxed">
                        {{ $dl->description }}
                    </p>

                    @if($dl->version)
                        <p class="text-[11px] text-[#788394]">Release Version: <span class="text-white font-medium">{{ $dl->version }}</span></p>
                    @endif
                </div>

                <!-- Mirror Buttons -->
                <div class="space-y-2 pt-4 border-t border-white/[0.06]">
                    <a href="{{ $dl->url_mirror1 }}" target="_blank" rel="noopener"
                       class="prestige-btn-primary w-full text-xs py-2.5">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Download Mirror 1</span>
                    </a>

                    @if($dl->url_mirror2)
                        <a href="{{ $dl->url_mirror2 }}" target="_blank" rel="noopener"
                           class="prestige-btn-outline w-full text-xs py-2">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Alternative Mirror 2</span>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- System Requirements Table -->
    <div class="prestige-card p-6 sm:p-8 space-y-4">
        <h3 class="font-cinzel text-lg font-bold text-white uppercase tracking-wider">System Specifications</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-[#AAB3C1]">
            <div class="space-y-2 bg-[#101725] p-4 rounded-xl border border-white/[0.06]">
                <strong class="font-cinzel text-[#C6A76A] block text-sm">Minimum Requirements</strong>
                <p>â€¢ OS: Windows 7 / 8 / 10 / 11</p>
                <p>â€¢ CPU: Intel Pentium 4 or AMD Athlon 64</p>
                <p>â€¢ Memory: 2 GB RAM</p>
                <p>â€¢ Storage: 5 GB available hard drive space</p>
                <p>â€¢ DirectX: Version 9.0c compatible</p>
            </div>
            <div class="space-y-2 bg-[#101725] p-4 rounded-xl border border-white/[0.06]">
                <strong class="font-cinzel text-[#2DD4BF] block text-sm">Recommended Specifications</strong>
                <p>â€¢ OS: Windows 10 / 11 64-bit</p>
                <p>â€¢ CPU: Intel Core i5 or AMD Ryzen 5</p>
                <p>â€¢ Memory: 8 GB RAM</p>
                <p>â€¢ Storage: High-speed SSD (10 GB available)</p>
                <p>â€¢ Graphics: Dedicated NVIDIA GeForce or AMD Radeon GPU</p>
            </div>
        </div>
    </div>
</div>
@endsection

