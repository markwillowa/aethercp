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
        .search-icon-centered {
            position: absolute;
            left: 14px;
            top: 50% !important;
            transform: translateY(-50%) !important;
            -webkit-transform: translateY(-50%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            pointer-events: none !important;
            z-index: 10 !important;
            line-height: 0 !important;
            width: 16px !important;
            height: 16px !important;
        }
        .search-icon-centered svg,
        .search-icon-centered i {
            display: block !important;
            width: 16px !important;
            height: 16px !important;
            margin: 0 !important;
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
<body x-data="{ devDonationOpen: false }" @open-dev-donation-modal.window="devDonationOpen = true" class="{{ $isChronicles ? 'bg-[#120D18] text-[#EDE4D2]' : (!empty($currentTheme['is_light']) ? 'bg-[#EDE2C7] text-[#3A2A1C]' : 'bg-[#070a13] text-slate-200') }} min-h-screen flex flex-col font-sans antialiased">
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
        @php
            $modMgr = app(\App\Services\Module\ModuleManager::class);
        @endphp
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
                    @if($modMgr->isEnabled('character'))
                        <a href="{{ route('admin.characters') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.characters*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="sword" class="w-4 h-4"></i>
                            <span>Characters</span>
                        </a>
                    @endif
                    @if($modMgr->isEnabled('guild'))
                        <a href="{{ route('admin.guilds') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.guilds*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span>Guilds</span>
                        </a>
                    @endif
                </div>
            </div>

            @php
                $hasContentRevenue = $modMgr->isEnabled('news') || $modMgr->isEnabled('downloads') || $modMgr->isEnabled('gallery') || $modMgr->isEnabled('marketplace') || $modMgr->isEnabled('support') || $modMgr->isEnabled('faq') || $modMgr->isEnabled('donations');
            @endphp
            @if($hasContentRevenue)
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider {{ $isChronicles ? 'text-[#D4AF37] font-cinzel border-b border-[#3E2E4E] pb-1' : (!empty($currentTheme['is_light']) ? 'text-[#FFDA89] border-b border-[#3D2616] pb-1' : 'text-slate-500') }} px-3 mb-2">Content &amp; Revenue</p>
                <div class="space-y-1 text-xs">
                    @if($modMgr->isEnabled('news'))
                        <a href="{{ route('admin.news.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.news*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="newspaper" class="w-4 h-4"></i>
                            <span>News &amp; Articles</span>
                        </a>
                    @endif
                    @if($modMgr->isEnabled('downloads'))
                        <a href="{{ route('admin.downloads.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.downloads*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="download-cloud" class="w-4 h-4"></i>
                            <span>Downloads Editor</span>
                        </a>
                    @endif
                    @if($modMgr->isEnabled('gallery'))
                        <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.gallery*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="image" class="w-4 h-4"></i>
                            <span>Gallery Manager</span>
                        </a>
                    @endif
                    @if($modMgr->isEnabled('marketplace'))
                        <a href="{{ route('admin.marketplace.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.marketplace*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                            <span>Player Marketplace</span>
                        </a>
                    @endif
                    <a href="{{ route('admin.cash-shop.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.cash-shop*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="store" class="w-4 h-4 text-amber-400"></i>
                        <span>Cash Shop / Item Mall</span>
                    </a>
                    @if($modMgr->isEnabled('support'))
                        <a href="{{ route('admin.support.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.support*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="life-buoy" class="w-4 h-4"></i>
                            <span>Support Center</span>
                        </a>
                    @endif
                    @if($modMgr->isEnabled('faq'))
                        <a href="{{ route('admin.faq.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.faq*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="help-circle" class="w-4 h-4"></i>
                            <span>FAQ &amp; Knowledge Base</span>
                        </a>
                    @endif
                    @if($modMgr->isEnabled('donations'))
                        <a href="{{ route('admin.donations') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ (request()->routeIs('admin.donations') && !request()->routeIs('admin.donations.packages*')) ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            <span>Donation Approvals</span>
                        </a>
                        <a href="{{ route('admin.donations.packages') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.donations.packages*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                            <i data-lucide="gift" class="w-4 h-4"></i>
                            <span>Packages &amp; Rewards</span>
                        </a>
                    @endif
                </div>
            </div>
            @endif

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
                    @if($modMgr->isEnabled('rankings'))
                    <a href="{{ route('admin.rankings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.rankings*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="trophy" class="w-4 h-4"></i>
                        <span>Rankings &amp; Sync</span>
                    </a>
                    <a href="{{ route('admin.badges.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.badges*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="award" class="w-4 h-4"></i>
                        <span>Badge Management</span>
                    </a>
                    <a href="{{ route('admin.rewards.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.rewards*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="gift" class="w-4 h-4"></i>
                        <span>Reward System</span>
                    </a>
                    @endif
                    @if($modMgr->isEnabled('attendance'))
                    <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.attendance*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>Daily Rewards</span>
                    </a>
                    @endif
                    <a href="{{ route('admin.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.logs*') ? ($isChronicles ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20') : ($isChronicles ? 'text-[#C8BDAA] hover:text-white hover:bg-[#251A33]' : 'text-slate-400 hover:text-white hover:bg-slate-900') }}">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Activity Logs</span>
                    </a>

                    <!-- Developer Donation Button -->
                    <button type="button" @click="devDonationOpen = true" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-semibold transition-all mt-3 group {{ $isChronicles ? 'bg-gradient-to-r from-[#D4AF37]/20 via-[#3A2410]/40 to-[#D4AF37]/10 text-[#FCE69B] border border-[#D4AF37]/50 hover:border-[#FFD700]' : 'bg-gradient-to-r from-amber-500/15 via-rose-500/10 to-indigo-500/15 text-amber-300 border border-amber-500/30 hover:border-amber-400 hover:text-white' }} shadow-sm">
                        <span class="flex items-center gap-2">
                            <i data-lucide="heart" class="w-4 h-4 text-rose-400 fill-rose-500/30 group-hover:scale-125 transition-transform"></i>
                            <span class="text-xs">Support Developer</span>
                        </span>
                        <span class="text-[9px] font-mono font-bold uppercase px-1.5 py-0.5 rounded tracking-wider {{ $isChronicles ? 'bg-[#D4AF37]/30 text-[#FFD700]' : 'bg-amber-400/20 text-amber-300 border border-amber-400/30' }}">Donate</span>
                    </button>
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

    <!-- Developer Donation Modal -->
    <div x-show="devDonationOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div @click.away="devDonationOpen = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-lg rounded-3xl bg-slate-900 border border-amber-500/30 p-6 sm:p-8 shadow-2xl overflow-hidden text-slate-200"
             x-data="{ copiedGCash: false, copiedBPI: false }">
            
            <!-- Glow background effect -->
            <div class="absolute -top-24 -right-24 w-60 h-60 bg-gradient-to-br from-amber-500/20 via-pink-500/20 to-transparent rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-start justify-between relative z-10 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-rose-500 p-0.5 shadow-lg shadow-rose-500/20">
                        <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                            <i data-lucide="heart" class="w-6 h-6 text-rose-400 fill-rose-500/30"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span>Support the Developer</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold uppercase">AetherCP</span>
                        </h3>
                        <p class="text-xs text-slate-400 font-semibold">Mark Willow Aldave</p>
                    </div>
                </div>
                <button type="button" @click="devDonationOpen = false" class="p-1.5 rounded-xl bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed mb-6">
                Thank you for using and administering <strong class="text-amber-300">Aether Control Panel</strong>! If you appreciate the continuous development, security patches, themes, and feature updates, you can support the developer by sending any amount of donation to his official accounts:
            </p>

            <div class="space-y-4 relative z-10 mb-6">
                <!-- GCash Account -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-blue-500/30 hover:border-blue-400/50 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400 shadow-[0_0_8px_#60a5fa]"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-400">GCash</span>
                        </div>
                        <button type="button" 
                                @click="navigator.clipboard.writeText('09653727778'); copiedGCash = true; setTimeout(() => copiedGCash = false, 2500)"
                                class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 border border-blue-500/40 transition-colors flex items-center gap-1.5">
                            <i data-lucide="copy" class="w-3.5 h-3.5" x-show="!copiedGCash"></i>
                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400" x-show="copiedGCash" style="display: none;"></i>
                            <span x-text="copiedGCash ? 'Copied!' : 'Copy Number'"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-mono font-bold text-white tracking-wider">09653727778</p>
                            <p class="text-[11px] text-slate-400 font-medium">Account Name: <strong class="text-slate-200">Mark Willow Aldave</strong></p>
                        </div>
                    </div>
                </div>

                <!-- BPI Account -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-red-500/30 hover:border-red-400/50 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400 shadow-[0_0_8px_#f87171]"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-red-400">BPI (Bank of the Philippine Islands)</span>
                        </div>
                        <button type="button" 
                                @click="navigator.clipboard.writeText('0856300202'); copiedBPI = true; setTimeout(() => copiedBPI = false, 2500)"
                                class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-red-500/20 text-red-300 hover:bg-red-500/30 border border-red-500/40 transition-colors flex items-center gap-1.5">
                            <i data-lucide="copy" class="w-3.5 h-3.5" x-show="!copiedBPI"></i>
                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400" x-show="copiedBPI" style="display: none;"></i>
                            <span x-text="copiedBPI ? 'Copied!' : 'Copy Account'"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-mono font-bold text-white tracking-wider">0856300202</p>
                            <p class="text-[11px] text-slate-400 font-medium">Account Name: <strong class="text-slate-200">Mark Willow Aldave</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-200/90 leading-relaxed mb-6">
                <p class="font-semibold text-amber-300 mb-1 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-4 h-4 text-amber-400"></i>
                    <span>How to send support:</span>
                </p>
                <ol class="list-decimal list-inside space-y-1 text-slate-300 pl-1">
                    <li>Open your GCash or BPI Banking mobile app.</li>
                    <li>Select <strong>Send Money / Transfer Funds</strong>.</li>
                    <li>Enter the account number and name (Mark Willow Aldave).</li>
                    <li>Input any amount you wish to contribute to the project.</li>
                </ol>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="devDonationOpen = false" class="px-5 py-2.5 rounded-xl font-bold bg-slate-800 hover:bg-slate-700 text-white text-xs transition-colors">
                    Close
                </button>
            </div>
        </div>
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
