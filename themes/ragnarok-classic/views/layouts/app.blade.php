<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" data-theme="ragnarok-classic">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('aether.name', 'AetherCP') }} - {{ config('aether.server_name', 'Ragnarok Online') }} [Classic]</title>
    <meta name="description" content="{{ config('aether.description') }}">

    <!-- Google Fonts & Tailwind Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Ragnarok Classic Theme Stylesheet -->
    <link rel="stylesheet" href="{{ route('theme.asset', ['theme' => 'ragnarok-classic', 'path' => 'theme.css']) }}">
</head>
<body class="min-h-screen flex flex-col font-sans antialiased text-[#3A2A1C] bg-[#E9D8B5]">
    <!-- Top Retro MMORPG Status Bar -->
    <header class="ro-header-banner z-20">
        <div class="max-w-7xl mx-auto px-4 py-2 flex flex-wrap items-center justify-between text-xs gap-3">
            <!-- Left: Server Rates & Episode -->
            <div class="flex items-center space-x-4 text-[#F8EBD2]">
                <span class="flex items-center gap-1.5 font-bold">
                    <i data-lucide="zap" class="w-3.5 h-3.5 text-[#E6C280]"></i>
                    <span>Rates:</span>
                    <span class="text-[#FFDA89]">{{ config('aether.rates.base') }} / {{ config('aether.rates.job') }} / {{ config('aether.rates.drop') }}</span>
                </span>
                <span class="text-[#7D5D39]">|</span>
                <span class="hidden md:flex items-center gap-1.5">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-[#A5D6A7]"></i>
                    <span>Mode:</span>
                    <span class="text-[#E7D5BE] font-medium">{{ config('emulator.renewal') ? 'Renewal' : 'Pre-Renewal' }}</span>
                </span>
                <span class="hidden md:inline-block text-[#7D5D39]">|</span>
                <span class="hidden md:flex items-center gap-1.5">
                    <i data-lucide="cpu" class="w-3.5 h-3.5 text-[#E6C280]"></i>
                    <span>Emulator:</span>
                    <span class="text-[#E7D5BE] uppercase font-bold">{{ config('emulator.driver') }}</span>
                </span>
            </div>

            <!-- Right: Server Status & Online Counter -->
            <div class="flex items-center space-x-3">
                @php
                    $onlineCount = app(\App\Contracts\EmulatorInterface::class)->getOnlinePlayerCount();
                    $loginStatus = app(\App\Contracts\EmulatorInterface::class)->checkServerStatus('login');
                @endphp
                <div class="flex items-center gap-2 bg-[#20140A]/80 border border-[#7D5D39] px-3 py-1 rounded">
                    @if($loginStatus['online'])
                        <span class="ro-status-online">
                            <span class="inline-block w-2 h-2 rounded-full bg-[#34D399]"></span>
                            ONLINE
                        </span>
                    @else
                        <span class="ro-status-offline">
                            <span class="inline-block w-2 h-2 rounded-full bg-[#EF4444]"></span>
                            OFFLINE
                        </span>
                    @endif
                    <span class="text-[#7D5D39]">|</span>
                    <span class="text-[#E7D5BE] text-xs">
                        Players: <strong class="text-[#FFDA89] font-bold">{{ $onlineCount }}</strong>
                    </span>
                </div>

                @if(config('aether.community.discord'))
                    <a href="{{ config('aether.community.discord') }}" target="_blank" rel="noopener" class="ro-btn-wood text-[11px] py-1 px-2.5">
                        <i data-lucide="message-square" class="w-3.5 h-3.5 text-[#9CBBE9]"></i>
                        <span>Discord</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Navigation Bar -->
    @include('components.navbar')

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="ro-panel p-3 border-l-4 border-l-[#34D399] flex items-center gap-3 bg-[#EBF7EE]">
                <i data-lucide="check-circle" class="w-5 h-5 text-[#2E7D32] shrink-0"></i>
                <p class="text-xs font-bold text-[#1B5E20]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="ro-panel p-3 border-l-4 border-l-[#EF4444] flex items-center gap-3 bg-[#FDEEED]">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-[#C62828] shrink-0"></i>
                <p class="text-xs font-bold text-[#B71C1C]">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Main Content Stage -->
    <main class="flex-1 max-w-7xl mx-auto px-4 py-6 w-full">
        @yield('content')
    </main>

    <!-- Classic Footer -->
    @include('components.footer')
</body>
</html>

