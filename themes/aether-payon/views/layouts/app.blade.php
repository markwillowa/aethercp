<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" data-theme="aether-payon">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('aether.name', 'AetherCP') }} - {{ config('aether.server_name', 'Payon Realm') }}</title>
    <meta name="description" content="{{ config('aether.description', 'Aether Payon — Traditional Asian Fantasy Ragnarok Online Experience.') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $cssAssetPath = base_path('themes/aether-payon/assets/theme.css');
        $cssVer = file_exists($cssAssetPath) ? filemtime($cssAssetPath) : '1.0';
    @endphp
    <link rel="stylesheet" href="{{ route('theme.asset', ['theme' => 'aether-payon', 'path' => 'theme.css']) }}?v={{ $cssVer }}">

    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen flex flex-col font-sans antialiased text-[#35291F] bg-[#F2E8CF]">

    @include('components.navbar')

    @if(session('success'))
        <div class="payon-container mt-4">
            <div class="payon-parchment-panel border-l-4 border-l-[#526D3A] px-4 py-3 flex items-center gap-3 text-xs font-semibold text-[#263D2C]">
                <i data-lucide="check-circle" class="w-4 h-4 text-[#526D3A] shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="payon-container mt-4">
            <div class="payon-parchment-panel border-l-4 border-l-[#8C2D2D] px-4 py-3 flex items-center gap-3 text-xs font-semibold text-[#6E1F1F]">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-[#8C2D2D] shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <main class="flex-grow relative z-10">
        @yield('content')
    </main>

    @include('components.footer')

    <script>
        function initPayonIcons() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            } else if (window.renderAetherIcons) {
                window.renderAetherIcons();
            }
        }
        document.addEventListener('DOMContentLoaded', initPayonIcons);
        window.addEventListener('load', initPayonIcons);
    </script>
</body>
</html>
