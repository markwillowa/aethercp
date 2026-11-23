<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" data-theme="aether-prestige">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('aether.name', 'Aether Prestige') }} - {{ config('aether.server_name', 'MYTHERA') }}</title>
    <meta name="description" content="{{ config('aether.description', 'Aether Prestige is a luxury Ragnarok Online experience with next-generation control panel integration.') }}">

    <!-- Google Fonts & Tailwind Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Aether Prestige Luxury Theme Stylesheet -->
    @php
        $cssAssetPath = base_path('themes/aether-prestige/assets/theme.css');
        $cssVer = file_exists($cssAssetPath) ? filemtime($cssAssetPath) : '1.0';
    @endphp
    <link rel="stylesheet" href="{{ route('theme.asset', ['theme' => 'aether-prestige', 'path' => 'theme.css']) }}?v={{ $cssVer }}">

    <!-- Lucide Icons Global Fallback Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen flex flex-col font-sans antialiased text-[#F4F5F7] bg-[#101725] selection:bg-[#C6A76A] selection:text-[#101725]">

    <!-- Main Navigation Bar -->
    @include('components.navbar')

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="relative z-30 prestige-container mt-4 w-full">
            <div class="bg-[#15231F] border border-emerald-500/40 text-emerald-300 px-4 py-3 rounded-xl flex items-center gap-3 shadow-lg shadow-black/40 backdrop-blur-md">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                <p class="text-xs font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="relative z-30 prestige-container mt-4 w-full">
            <div class="bg-[#2A1517] border border-red-500/40 text-red-300 px-4 py-3 rounded-xl flex items-center gap-3 shadow-lg shadow-black/40 backdrop-blur-md">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-400 shrink-0"></i>
                <p class="text-xs font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Page Main Content -->
    <main class="flex-grow relative z-10">
        @yield('content')
    </main>

    <!-- Global Video / Trailer Modal -->
    <div id="prestigeVideoModal" class="prestige-modal-backdrop" onclick="closePrestigeModal(event)">
        <div class="relative w-full max-w-4xl bg-[#151B26] border border-white/20 rounded-2xl overflow-hidden shadow-2xl p-2" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
                <div class="flex items-center gap-2">
                    <i data-lucide="film" class="w-4 h-4 text-[#C6A76A]"></i>
                    <h4 id="prestigeModalTitle" class="text-xs uppercase tracking-wider font-bold text-white font-cinzel">Cinematic Showcase</h4>
                </div>
                <button type="button" onclick="closePrestigeModal()" class="text-white/60 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="relative w-full pt-[56.25%] bg-black">
                <iframe id="prestigeModalIframe" class="absolute inset-0 w-full h-full" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <!-- Main Footer -->
    @include('components.footer')

    <!-- Lucide Icons & Global Modal Script -->
    <script>
        function initializePrestigeIcons() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            } else if (window.renderAetherIcons) {
                window.renderAetherIcons();
            }
        }
        document.addEventListener('DOMContentLoaded', initializePrestigeIcons);
        window.addEventListener('load', initializePrestigeIcons);

        function openPrestigeTrailer(videoUrl, title) {
            const modal = document.getElementById('prestigeVideoModal');
            const iframe = document.getElementById('prestigeModalIframe');
            const titleEl = document.getElementById('prestigeModalTitle');
            if (modal && iframe) {
                iframe.src = videoUrl + (videoUrl.includes('?') ? '&' : '?') + 'autoplay=1';
                if (titleEl && title) titleEl.innerText = title;
                modal.classList.add('open');
            }
        }

        function closePrestigeModal() {
            const modal = document.getElementById('prestigeVideoModal');
            const iframe = document.getElementById('prestigeModalIframe');
            if (modal && iframe) {
                modal.classList.remove('open');
                iframe.src = '';
            }
        }
    </script>
</body>
</html>
