<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ !empty($currentTheme['is_light']) ? 'light' : 'dark' }}" data-theme="{{ $currentTheme['slug'] ?? 'aether-dark' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AetherCP Administration - {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
    </style>

    @if(!empty($currentTheme['has_css']) && !empty($currentTheme['slug']))
        <link rel="stylesheet" href="{{ route('theme.asset', ['theme' => $currentTheme['slug'], 'path' => 'theme.css']) }}">
    @endif
</head>
<body class="{{ !empty($currentTheme['is_light']) ? 'bg-[#EDE2C7] text-[#3A2A1C]' : 'bg-[#070a13] text-slate-200' }} min-h-screen flex flex-col font-sans antialiased">
    <!-- Top Admin Bar -->
    <header class="border-b border-slate-800 bg-[#0b0f19] sticky top-0 z-30">
        <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 font-bold">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-white tracking-wide">AetherCP <span class="text-amber-400">Admin</span></span>
                        <span class="block text-[10px] text-slate-500">{{ config('aether.server_name') }}</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 flex items-center gap-1.5 transition-colors">
                    <i data-lucide="globe" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>View Public Site</span>
                </a>
                <span class="text-xs text-slate-400">|</span>
                <span class="text-xs font-semibold text-cyan-400">{{ auth()->user()->username }}</span>
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
        <aside class="w-64 border-r border-slate-800 bg-[#070a13] p-4 space-y-6 hidden md:block shrink-0">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Operations</p>
                <div class="space-y-1 text-xs">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.accounts') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.accounts*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Game Accounts</span>
                    </a>
                    <a href="{{ route('admin.characters') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.characters*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="sword" class="w-4 h-4"></i>
                        <span>Characters</span>
                    </a>
                    <a href="{{ route('admin.guilds') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.guilds*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                        <span>Guilds</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Content & Revenue</p>
                <div class="space-y-1 text-xs">
                    <a href="{{ route('admin.news.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.news*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="newspaper" class="w-4 h-4"></i>
                        <span>News & Articles</span>
                    </a>
                    <a href="{{ route('admin.donations') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.donations*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>Donation Approvals</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">System</p>
                <div class="space-y-1 text-xs">
                    <a href="{{ route('admin.modules') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.modules*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="package" class="w-4 h-4"></i>
                        <span>Modules Manager</span>
                    </a>
                    <a href="{{ route('admin.themes') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.themes*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="palette" class="w-4 h-4"></i>
                        <span>Theme Manager</span>
                    </a>
                    <a href="{{ route('admin.importer') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.importer*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="database" class="w-4 h-4"></i>
                        <span>Database & YAML Importer</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i data-lucide="sliders" class="w-4 h-4"></i>
                        <span>Server Settings</span>
                    </a>
                    <a href="{{ route('admin.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.logs*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
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

            @yield('admin_content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.renderAetherIcons) {
                window.renderAetherIcons();
            }
        });
    </script>
</body>
</html>
