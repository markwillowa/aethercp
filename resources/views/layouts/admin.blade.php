<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ !empty($currentTheme['is_light']) ? 'light' : 'dark' }}" data-theme="{{ $currentTheme['slug'] ?? 'aether-dark' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AetherCP Administration - {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts: Cinzel & Inter for classic MMORPG theme typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons Global Fallback Script -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @php
        $isChronicles = ($currentTheme['slug'] ?? '') === 'aether-chronicles';
    @endphp

    <!-- Dynamic Theme Styling & CSS Injection -->
    <style>
        :root {
            --color-theme-primary: {{ $currentTheme['colors']['primary'] ?? '#06b6d4' }};
            --color-theme-accent: {{ $currentTheme['colors']['accent'] ?? '#3b82f6' }};
            --color-theme-bg: {{ $currentTheme['colors']['background'] ?? '#070a13' }};
            --color-theme-surface: {{ $currentTheme['colors']['surface'] ?? '#0f172a' }};
        }
        html, body {
            background-color: var(--color-theme-bg) !important;
            color: {{ !empty($currentTheme['is_light']) ? '#3A2A1C' : '#e2e8f0' }} !important;
        }

        /* =====================================================================
           AETHER CHRONICLES: GLOBAL ADMIN PANEL THEME HARMONIZATION
           Matches the active theme's Royal Purple, Antique Gold & Stone styling
           ===================================================================== */
        html[data-theme="aether-chronicles"] body {
            background-color: #120D18 !important;
            color: #EDE4D2 !important;
            font-family: 'Inter', system-ui, sans-serif;
        }

        html[data-theme="aether-chronicles"] .admin-header {
            background-color: #1B1324 !important;
            border-bottom: 2px solid #5A456C !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6) !important;
        }

        html[data-theme="aether-chronicles"] .admin-sidebar {
            background-color: #160F1F !important;
            border-right: 2px solid #4A3A5A !important;
            color: #C8BDAA !important;
        }

        html[data-theme="aether-chronicles"] h1,
        html[data-theme="aether-chronicles"] h2,
        html[data-theme="aether-chronicles"] h3 {
            font-family: 'Cinzel', Georgia, serif;
            color: #F3E5AB !important;
            letter-spacing: 0.03em;
        }

        /* Cards, Metric Widgets, and Panels */
        html[data-theme="aether-chronicles"] .bg-slate-900,
        html[data-theme="aether-chronicles"] .bg-slate-900\/80,
        html[data-theme="aether-chronicles"] .bg-slate-900\/70,
        html[data-theme="aether-chronicles"] .bg-slate-950 {
            background-color: #1C1425 !important;
            border-color: #4A3A5A !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4) !important;
        }

        html[data-theme="aether-chronicles"] .border-slate-800 {
            border-color: #3E2F4D !important;
        }

        html[data-theme="aether-chronicles"] .divide-slate-800 > :not([hidden]) ~ :not([hidden]) {
            border-color: #3E2F4D !important;
        }

        /* Metric Numbers & Headings */
        html[data-theme="aether-chronicles"] .text-3xl {
            color: #FFF0D4 !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }

        /* Replace Cyan Accents with Antique Gold */
        html[data-theme="aether-chronicles"] .text-cyan-400 {
            color: #E5B358 !important;
        }
        html[data-theme="aether-chronicles"] .text-cyan-500 {
            color: #D4AF37 !important;
        }

        html[data-theme="aether-chronicles"] .bg-cyan-500\/10 {
            background-color: rgba(212, 175, 55, 0.15) !important;
            border-color: rgba(212, 175, 55, 0.4) !important;
            color: #FCE69B !important;
        }

        html[data-theme="aether-chronicles"] .border-cyan-500\/20 {
            border-color: rgba(212, 175, 55, 0.4) !important;
        }

        /* Primary Action Buttons (Gold Gradient) */
        html[data-theme="aether-chronicles"] .bg-cyan-500,
        html[data-theme="aether-chronicles"] .bg-cyan-600 {
            background: linear-gradient(180deg, #E5B358 0%, #B8860B 100%) !important;
            color: #17111F !important;
            font-weight: 700 !important;
            border: 1px solid #FCE69B !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4) !important;
        }

        html[data-theme="aether-chronicles"] .hover\:bg-cyan-400:hover,
        html[data-theme="aether-chronicles"] .hover\:bg-cyan-500:hover {
            background: linear-gradient(180deg, #F5C66C 0%, #CD9A1A 100%) !important;
            color: #0E0A13 !important;
        }

        /* Inputs, Selects & Textareas */
        html[data-theme="aether-chronicles"] input,
        html[data-theme="aether-chronicles"] select,
        html[data-theme="aether-chronicles"] textarea {
            background-color: #140E1B !important;
            border: 1px solid #4A3A5A !important;
            color: #EDE4D2 !important;
        }

        html[data-theme="aether-chronicles"] input:focus,
        html[data-theme="aether-chronicles"] select:focus,
        html[data-theme="aether-chronicles"] textarea:focus {
            border-color: #D4AF37 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.3) !important;
        }

        /* Tables */
        html[data-theme="aether-chronicles"] th {
            background-color: #241A2F !important;
            color: #F3E5AB !important;
            border-color: #4A3A5A !important;
            font-family: 'Cinzel', serif !important;
            letter-spacing: 0.05em;
            font-size: 11px !important;
        }

        html[data-theme="aether-chronicles"] td {
            border-color: #352643 !important;
            color: #EDE4D2 !important;
        }

        html[data-theme="aether-chronicles"] tr:hover td {
            background-color: #261D32 !important;
        }

        /* Secondary Buttons & Links */
        html[data-theme="aether-chronicles"] .bg-slate-800 {
            background-color: #281D35 !important;
            border-color: #5A456C !important;
            color: #EDE4D2 !important;
        }

        html[data-theme="aether-chronicles"] .hover\:bg-slate-800:hover,
        html[data-theme="aether-chronicles"] .hover\:bg-slate-900:hover {
            background-color: #332544 !important;
            color: #FFF !important;
        }

        /* Muted & Secondary Text */
        html[data-theme="aether-chronicles"] .text-slate-400 {
            color: #C8BDAA !important;
        }
        html[data-theme="aether-chronicles"] .text-slate-500 {
            color: #9A8E80 !important;
        }
    </style>

    @if(!empty($currentTheme['has_css']) && !empty($currentTheme['slug']))
        @php
            $cssAssetPath = base_path('themes/' . $currentTheme['slug'] . '/assets/theme.css');
            $cssVer = file_exists($cssAssetPath) ? filemtime($cssAssetPath) : '1.0';
        @endphp
        <link rel="stylesheet" href="{{ route('theme.asset', ['theme' => $currentTheme['slug'], 'path' => 'theme.css']) }}?v={{ $cssVer }}">
    @endif
</head>
<body class="{{ $isChronicles ? 'bg-[#120D18] text-[#EDE4D2]' : (!empty($currentTheme['is_light']) ? 'bg-[#EDE2C7] text-[#3A2A1C]' : 'bg-[#070a13] text-slate-200') }} min-h-screen flex flex-col font-sans antialiased">
    <!-- Top Admin Bar -->
    <header class="admin-header border-b {{ $isChronicles ? 'border-[#5A456C] bg-[#1B1324]' : (!empty($currentTheme['is_light']) ? 'border-[#8C6A2D] bg-[#2D1D13]' : 'border-slate-800 bg-[#0b0f19]') }} sticky top-0 z-30">
        <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg {{ $isChronicles ? 'bg-[#D4AF37]/20 border border-[#D4AF37]/50 text-[#FCE69B]' : (!empty($currentTheme['is_light']) ? 'bg-[#4A301D] border border-[#FFDA89] text-[#FFE082]' : 'bg-amber-500/20 border border-amber-500/40 text-amber-400') }} flex items-center justify-center font-bold shrink-0">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    </div>
                    <div class="shrink-0">
                        <span class="text-sm font-bold {{ $isChronicles ? 'text-[#F3E5AB] font-cinzel' : (!empty($currentTheme['is_light']) ? 'text-[#FFF0D4]' : 'text-white') }} tracking-wide">
                            AetherCP <span class="{{ $isChronicles ? 'text-[#E5B358]' : 'text-amber-400' }}">Admin</span>
                        </span>
                        <span class="block text-[10px] {{ $isChronicles ? 'text-[#C8BDAA]' : (!empty($currentTheme['is_light']) ? 'text-[#C5B090]' : 'text-slate-500') }}">{{ config('aether.server_name') }}</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $isChronicles ? 'bg-[#281D35] border border-[#5A456C] text-[#EDE4D2] hover:border-[#D4AF37] hover:text-[#FFD700]' : (!empty($currentTheme['is_light']) ? 'bg-[#315B88] border border-[#6392C2] text-[#F8EBD2] hover:bg-[#3d6e9f]' : 'bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800') }} flex items-center gap-1.5 transition-colors">
                    <i data-lucide="globe" class="w-3.5 h-3.5 {{ $isChronicles ? 'text-[#D4AF37]' : 'text-cyan-400' }}"></i>
                    <span>View Public Site</span>
                </a>
                <span class="text-xs text-slate-400">|</span>
                <span class="text-xs font-semibold {{ $isChronicles ? 'text-[#FCE69B] font-cinzel' : (!empty($currentTheme['is_light']) ? 'text-[#FFDA89]' : 'text-cyan-400') }}">{{ auth()->user()?->username ?? 'Administrator' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-400 rounded-lg hover:bg-slate-900 transition-colors" title="Log Out">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex-grow flex">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar w-64 border-r {{ $isChronicles ? 'border-[#4A3A5A] bg-[#160F1F] text-[#C8BDAA]' : (!empty($currentTheme['is_light']) ? 'border-[#8C6A2D] bg-[#22140B] text-[#E7D5BE]' : 'border-slate-800 bg-[#070a13] text-slate-400') }} p-4 space-y-6 hidden md:block shrink-0">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider {{ $isChronicles ? 'text-[#D4AF37] font-cinzel border-b border-[#3E2E4E] pb-1' : (!empty($currentTheme['is_light']) ? 'text-[#FFDA89] border-b border-[#3D2616] pb-1' : 'text-slate-500') }} px-3 mb-2">Operations</p>
                <div class="space-y-1 text-xs">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.accounts') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.accounts*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Game Accounts</span>
                    </a>
                    <a href="{{ route('admin.characters') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.characters*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="sword" class="w-4 h-4"></i>
                        <span>Characters</span>
                    </a>
                    <a href="{{ route('admin.guilds') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.guilds*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                        <span>Guilds</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider {{ $isChronicles ? 'text-[#D4AF37] font-cinzel border-b border-[#3E2E4E] pb-1' : (!empty($currentTheme['is_light']) ? 'text-[#FFDA89] border-b border-[#3D2616] pb-1' : 'text-slate-500') }} px-3 mb-2">Content &amp; Revenue</p>
                <div class="space-y-1 text-xs">
                    <a href="{{ route('admin.news.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.news*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="newspaper" class="w-4 h-4"></i>
                        <span>News &amp; Articles</span>
                    </a>
                    <a href="{{ route('admin.downloads.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.downloads*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="download-cloud" class="w-4 h-4"></i>
                        <span>Downloads Editor</span>
                    </a>
                    <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.gallery*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="image" class="w-4 h-4"></i>
                        <span>Gallery Manager</span>
                    </a>
                    <a href="{{ route('admin.marketplace.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.marketplace*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        <span>Player Marketplace</span>
                    </a>
                    <a href="{{ route('admin.support.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.support*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="life-buoy" class="w-4 h-4"></i>
                        <span>Support Center</span>
                    </a>
                    <a href="{{ route('admin.faq.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.faq*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="help-circle" class="w-4 h-4"></i>
                        <span>FAQ &amp; Knowledge Base</span>
                    </a>
                    <a href="{{ route('admin.donations') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ (request()->routeIs('admin.donations') && !request()->routeIs('admin.donations.packages*')) ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>Donation Approvals</span>
                    </a>
                    <a href="{{ route('admin.donations.packages') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.donations.packages*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="gift" class="w-4 h-4"></i>
                        <span>Packages &amp; Rewards</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider {{ $isChronicles ? 'text-[#D4AF37] font-cinzel border-b border-[#3E2E4E] pb-1' : (!empty($currentTheme['is_light']) ? 'text-[#FFDA89] border-b border-[#3D2616] pb-1' : 'text-slate-500') }} px-3 mb-2">System</p>
                <div class="space-y-1 text-xs">
                    <a href="{{ route('admin.modules') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.modules*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="package" class="w-4 h-4"></i>
                        <span>Modules Manager</span>
                    </a>
                    <a href="{{ route('admin.themes') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.themes*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="palette" class="w-4 h-4"></i>
                        <span>Theme Manager</span>
                    </a>
                    <a href="{{ route('admin.importer') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.importer*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="database" class="w-4 h-4"></i>
                        <span>Database &amp; YAML Importer</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.settings*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="sliders" class="w-4 h-4"></i>
                        <span>Server Settings</span>
                    </a>
                    <a href="{{ route('admin.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.logs*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Activity Logs</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="flex-grow p-6 lg:p-8 space-y-6 max-w-7xl">
            @if(session('success'))
                <div class="bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-xl flex items-center gap-3 text-xs">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-950/80 border border-red-500/40 text-red-200 px-4 py-3 rounded-xl flex items-center gap-3 text-xs">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-400 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(View::hasSection('admin_content'))
                @yield('admin_content')
            @else
                @yield('content')
            @endif
        </main>
    </div>

    <script>
        function initializeAdminIcons() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            } else if (window.renderAetherIcons) {
                window.renderAetherIcons();
            }
        }
        document.addEventListener('DOMContentLoaded', initializeAdminIcons);
        window.addEventListener('load', initializeAdminIcons);
    </script>
</body>
</html>
