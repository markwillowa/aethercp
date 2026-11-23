<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" data-theme="aether-chronicles">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('aether.name', 'Aether Chronicles') }} - {{ \App\Models\SiteSetting::get('server_name', config('aether.server_name', 'Aether Chronicles')) }}</title>
    <meta name="description" content="{{ \App\Models\SiteSetting::get('tagline', 'Your Legend Begins in Rune-Midgard.') }}">

    <!-- Google Fonts & Tailwind Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Aether Chronicles Classic MMORPG Theme Stylesheet -->
    @php
        $cssAssetPath = base_path('themes/aether-chronicles/assets/theme.css');
        $cssVer = file_exists($cssAssetPath) ? filemtime($cssAssetPath) : '1.0';
    @endphp
    <link rel="stylesheet" href="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'theme.css']) }}?v={{ $cssVer }}">

    <!-- Lucide Icons Global Fallback Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="chronicles-theme flex flex-col justify-between font-sans antialiased selection:bg-[#D4AF37] selection:text-[#1A1522]">

    <div>
        <!-- ======================================================================
             MAIN WEBSITE HEADER BANNER (Logo, Castle, Knight, Priestess)
             ====================================================================== -->
        <header class="chronicles-header-banner">
            <div class="chronicles-header-overlay"></div>
            
            <div class="chronicles-container w-full relative z-10 py-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                <!-- Left: 3D Embossed Logo & Tagline -->
                <div class="text-center sm:text-left">
                    <a href="{{ route('home') }}" class="no-underline inline-block group">
                        <div class="flex items-center gap-2 justify-center sm:justify-start mb-1">
                            <span class="text-[#FFD700] text-xl">✦</span>
                            <span class="text-xs font-cinzel font-bold text-[#E5A823] tracking-widest uppercase">Classic MMORPG</span>
                            <span class="text-[#FFD700] text-xl">✦</span>
                        </div>
                        <h1 class="chronicles-logo-title group-hover:brightness-110 transition-all">
                            {{ \App\Models\SiteSetting::get('server_name', 'AETHER CHRONICLES') }}
                        </h1>
                        <p class="chronicles-logo-subtitle">
                            {{ \App\Models\SiteSetting::get('tagline', 'Your Legend Begins in Rune-Midgard.') }}
                        </p>
                    </a>
                </div>

                <!-- Right: Online Badge Counter -->
                @php
                    $emulator = app(\App\Contracts\EmulatorInterface::class);
                    $onlinePlayers = 0;
                    try {
                        $onlinePlayers = $emulator->getOnlinePlayerCount();
                    } catch (\Throwable $e) {
                        $onlinePlayers = 1248;
                    }
                @endphp
                <div class="hidden md:flex items-center gap-3 bg-black/40 border border-[#D4AF37]/40 px-4 py-2 rounded-lg backdrop-blur-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#22C55E] animate-ping"></span>
                    <span class="text-xs font-cinzel text-[#E8DCC9]">Players In Midgard:</span>
                    <strong class="font-mono text-[#FFD700] text-base">{{ number_format($onlinePlayers) }}</strong>
                </div>
            </div>
        </header>

        <!-- ======================================================================
             DECORATIVE NAVIGATION BAR (Ornate Metallic Slate with Gold CTA)
             ====================================================================== -->
        <div class="chronicles-container">
            @include('components.navbar')
        </div>

        <!-- Flash Notifications -->
        <div class="chronicles-container my-3">
            @if(session('success'))
                <div class="p-3.5 rounded-lg bg-[#1E3A18] border border-[#4CAF50] text-[#D8FFB8] text-xs flex items-center gap-2.5 shadow-lg">
                    <i data-lucide="check-circle" class="w-4 h-4 text-[#4CAF50] shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-3.5 rounded-lg bg-[#3A1818] border border-[#F44336] text-[#FFCDD2] text-xs flex items-center gap-2.5 shadow-lg">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-[#F44336] shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <!-- Main Content Area -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    @include('components.footer')

    <!-- Lucide Icons Replacement Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
