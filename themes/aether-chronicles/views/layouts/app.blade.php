<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" data-theme="aether-chronicles">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Aether Chronicles' }} - {{ \App\Models\SiteSetting::get('server_name', 'Aether Chronicles') }}</title>
    <meta name="description" content="{{ \App\Models\SiteSetting::get('chronicles_tagline', 'Your Legend Begins in Rune-Midgard.') }}">

    <!-- Google Fonts & Tailwind Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Aether Chronicles Classic MMORPG Theme Stylesheet -->
    @php
        $cssAssetPath = base_path('themes/aether-chronicles/assets/theme.css');
        $cssVer = file_exists($cssAssetPath) ? filemtime($cssAssetPath) : '5.0';
        $headerBgVal = \App\Models\SiteSetting::get('chronicles_header_bg');
        if (!empty($headerBgVal)) {
            if (\Illuminate\Support\Str::startsWith($headerBgVal, ['http://', 'https://', '//', '/'])) {
                $headerBg = $headerBgVal;
            } else {
                $headerBg = route('theme.asset', ['theme' => 'aether-chronicles', 'path' => $headerBgVal]);
            }
        } else {
            $headerBg = route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/header_chronicles_hero_clean.jpg']);
        }
    @endphp
    <link rel="stylesheet" href="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'theme.css']) }}?v={{ $cssVer }}">

    <!-- Lucide Icons Global Fallback Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="chronicles-theme flex flex-col justify-between font-sans antialiased selection:bg-[#D4AF37] selection:text-[#1A1522]">

    <div>
        <!-- ======================================================================
             1. MAIN WEBSITE HEADER BANNER (Castle, Knight, Priestess & Crest)
             ====================================================================== -->
        <header class="chronicles-header-banner" style="background-image: url('{{ $headerBg }}');">
            <div class="chronicles-header-overlay"></div>
            
            <div class="chronicles-container w-full relative z-10 py-6 flex items-center justify-between">
                <!-- Left: 3D Embossed Logo & Tagline -->
                @php
                    $customLogo = \App\Models\SiteSetting::get('chronicles_logo');
                    $serverName = \App\Models\SiteSetting::get('server_name', 'AETHER CHRONICLES');
                    $tagline = \App\Models\SiteSetting::get('chronicles_tagline', 'Your Legend Begins in Rune-Midgard.');
                @endphp

                <a href="{{ route('home') }}" class="no-underline inline-block group">
                    @if(!empty($customLogo))
                        <img src="{{ $customLogo }}" alt="{{ $serverName }}" class="h-16 object-contain">
                    @else
                        <div class="chronicles-logo-badge">
                            <span>✦</span>
                            <span>Classic MMORPG</span>
                            <span>✦</span>
                        </div>
                        <h1 class="chronicles-logo-title group-hover:brightness-110 transition-all">
                            {{ $serverName }}
                        </h1>
                        <p class="chronicles-logo-subtitle">
                            {{ $tagline }}
                        </p>
                    @endif
                </a>

                <!-- Right: Online Adventurers Pill Badge (Live Server Status) -->
                @php
                    $emulator = app(\App\Contracts\EmulatorInterface::class);
                    $onlinePlayers = 0;
                    $isClusterOnline = false;
                    try {
                        $onlinePlayers = $emulator->getOnlinePlayerCount();
                        $statusLogin = $emulator->checkServerStatus('login');
                        $isClusterOnline = !empty($statusLogin['online']) || $onlinePlayers > 0;
                    } catch (\Throwable $e) {
                        $onlinePlayers = 0;
                        $isClusterOnline = false;
                    }
                @endphp
                <div class="hidden md:flex items-center gap-2.5 chronicles-online-pill self-end mb-2">
                    @if($isClusterOnline)
                        <span class="w-2.5 h-2.5 rounded-full bg-[#22C55E] animate-ping"></span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-[#EF4444]"></span>
                    @endif
                    <span class="text-xs font-cinzel text-[#E8DCC9]">Players In Midgard:</span>
                    <strong class="font-mono {{ $onlinePlayers > 0 ? 'text-[#FFD700]' : 'text-[#8C7F99]' }} text-sm">{{ number_format($onlinePlayers) }}</strong>
                </div>
            </div>
        </header>

        <!-- ======================================================================
             2. DECORATIVE NAVIGATION BAR (Full Width Medieval Stone)
             ====================================================================== -->
        <div class="chronicles-container">
            @include('components.navbar')
        </div>

        <!-- Flash Notifications -->
        <div class="chronicles-container my-2">
            @if(session('success'))
                <div class="p-3 rounded bg-[#1E3A18] border border-[#4CAF50] text-[#D8FFB8] text-xs flex items-center gap-2.5 shadow-lg">
                    <i data-lucide="check-circle" class="w-4 h-4 text-[#4CAF50] shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-3 rounded bg-[#3A1818] border border-[#F44336] text-[#FFCDD2] text-xs flex items-center gap-2.5 shadow-lg">
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

    <!-- Lucide Icons Initializer -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
