@extends('layouts.admin')

@section('admin_content')
<div x-data="{ activeTab: 'general' }" class="max-w-5xl space-y-6">
    <div class="border-b border-slate-800 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Website &amp; Realm Settings</h1>
            <p class="text-xs text-slate-400 mt-1">Configure live server rates, server identity, home page headlines, trailers, features, and payment gateways.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" form="admin-settings-form" class="py-2.5 px-6 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save All Settings</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-2">
        <button type="button" @click="activeTab = 'general'"
            :class="activeTab === 'general' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'text-slate-400 hover:text-white border-transparent'"
            class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
            <i data-lucide="server" class="w-4 h-4"></i>
            <span>Server &amp; Rates</span>
        </button>

        <button type="button" @click="activeTab = 'hero'"
            :class="activeTab === 'hero' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'text-slate-400 hover:text-white border-transparent'"
            class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
            <i data-lucide="film" class="w-4 h-4"></i>
            <span>Home Hero &amp; Trailers</span>
        </button>

        <button type="button" @click="activeTab = 'features'"
            :class="activeTab === 'features' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'text-slate-400 hover:text-white border-transparent'"
            class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
            <i data-lucide="sparkles" class="w-4 h-4"></i>
            <span>Home 6 Features</span>
        </button>

        <button type="button" @click="activeTab = 'telemetry_cta'"
            :class="activeTab === 'telemetry_cta' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'text-slate-400 hover:text-white border-transparent'"
            class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
            <i data-lucide="layout-template" class="w-4 h-4"></i>
            <span>Telemetry &amp; CTA Banner</span>
        </button>

        <button type="button" @click="activeTab = 'social'"
            :class="activeTab === 'social' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'text-slate-400 hover:text-white border-transparent'"
            class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
            <i data-lucide="share-2" class="w-4 h-4"></i>
            <span>Community &amp; Socials</span>
        </button>

        <button type="button" @click="activeTab = 'paypal'"
            :class="activeTab === 'paypal' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'text-slate-400 hover:text-white border-transparent'"
            class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
            <i data-lucide="credit-card" class="w-4 h-4"></i>
            <span>PayPal Gateway</span>
        </button>

        <button type="button" @click="activeTab = 'chronicles'"
            :class="activeTab === 'chronicles' ? 'bg-amber-500/10 text-amber-400 border-amber-500/30' : 'text-slate-400 hover:text-white border-transparent'"
            class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2">
            <i data-lucide="crown" class="w-4 h-4 text-amber-400"></i>
            <span>Aether Chronicles</span>
        </button>
    </div>

    <!-- Main Settings Form -->
    <form id="admin-settings-form" method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf

        <!-- TAB 1: SERVER & RATES -->
        <div x-show="activeTab === 'general'" class="space-y-6">
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="shield" class="w-4 h-4 text-cyan-400"></i>
                        <span>Server Identity &amp; Meta</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Brand identity used across the navbar, footer, meta tags, and hero titles.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Server Name</label>
                        <input type="text" name="server_name" value="{{ $settings['server_name'] ?? 'MYTHERA' }}" required
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        <p class="text-[10px] text-slate-500 mt-1">Displays on the main branding crest, navbar, and hero screen.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Tagline / Motto</label>
                        <input type="text" name="tagline" value="{{ $settings['tagline'] ?? 'Next-Generation Ragnarok Online Experience' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Client Version</label>
                        <input type="text" name="client_version" value="{{ $settings['client_version'] ?? '2023-11-01' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Server Mode</label>
                        <select name="server_mode" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                            <option value="Renewal" {{ ($settings['server_mode'] ?? 'Renewal') === 'Renewal' ? 'selected' : '' }}>Renewal</option>
                            <option value="Pre-Renewal" {{ ($settings['server_mode'] ?? '') === 'Pre-Renewal' ? 'selected' : '' }}>Pre-Renewal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Emulator Driver</label>
                        <input type="text" name="server_emulator" value="{{ $settings['server_emulator'] ?? 'rAthena' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>
            </div>

            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-4 h-4 text-cyan-400"></i>
                        <span>Server Experience &amp; Drop Rates</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Displayed in the hero rates strip, meta cards, and footer specifications.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Base Exp Rate</label>
                        <input type="text" name="base_rate" value="{{ $settings['base_rate'] ?? '1000x' }}" required
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Job Exp Rate</label>
                        <input type="text" name="job_rate" value="{{ $settings['job_rate'] ?? '1000x' }}" required
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Item Drop Rate</label>
                        <input type="text" name="drop_rate" value="{{ $settings['drop_rate'] ?? '100x' }}" required
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Max Base Level</label>
                        <input type="number" name="max_base_level" value="{{ $settings['max_base_level'] ?? '260' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Max Job Level</label>
                        <input type="number" name="max_job_level" value="{{ $settings['max_job_level'] ?? '55' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: HOME HERO & TRAILERS -->
        <div x-show="activeTab === 'hero'" class="space-y-6">
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="tv" class="w-4 h-4 text-cyan-400"></i>
                        <span>Hero Headline &amp; Call To Action</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Customize the title, subtitle, storyline description, and action buttons on the home hero.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Hero Subtitle</label>
                        <input type="text" name="home_hero_subtitle" value="{{ $settings['home_hero_subtitle'] ?? 'The Lost Realms' }}"
                            placeholder="The Lost Realms"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Hero Description Paragraph</label>
                        <textarea name="home_hero_desc" rows="3"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">{{ $settings['home_hero_desc'] ?? 'An epic open-world fantasy RPG where ancient powers clash, kingdoms fall, and heroes rise.' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Register Button Text</label>
                            <input type="text" name="home_hero_cta_text" value="{{ $settings['home_hero_cta_text'] ?? 'Register Free' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Main Trailer Button Text</label>
                            <input type="text" name="home_hero_trailer_title" value="{{ $settings['home_hero_trailer_title'] ?? 'Cinematic Reveal Trailer' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Main Reveal Trailer Embed URL</label>
                        <input type="url" name="home_hero_trailer_url" value="{{ $settings['home_hero_trailer_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ' }}"
                            placeholder="https://www.youtube.com/embed/..."
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        <p class="text-[10px] text-slate-500 mt-1">Must be a valid embed URL (e.g. https://www.youtube.com/embed/VIDEO_ID).</p>
                    </div>
                </div>
            </div>

            <!-- 3 Vertically Stacked Trailers -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i data-lucide="film" class="w-4 h-4 text-cyan-400"></i>
                            <span>Hero Right-Column Cinematic Trailers</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Configure the 3 playable preview cards (01, 02, 03) on the right side of the hero screen.</p>
                    </div>
                    <div class="w-48">
                        <input type="text" name="home_trailers_header" value="{{ $settings['home_trailers_header'] ?? 'Trailers' }}"
                            placeholder="Header (e.g. Trailers)"
                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-lg text-xs text-slate-200 text-right">
                    </div>
                </div>

                <!-- Trailer 01 -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-cyan-500/20 text-cyan-400 text-xs font-bold flex items-center justify-center font-mono">01</span>
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Trailer 01</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Title / Caption</label>
                            <input type="text" name="home_trailer_1_title" value="{{ $settings['home_trailer_1_title'] ?? 'Chapter I: The Awakening' }}"
                                class="w-full px-3 py-2 bg-slate-900 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">YouTube Embed URL</label>
                            <input type="url" name="home_trailer_1_url" value="{{ $settings['home_trailer_1_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ' }}"
                                class="w-full px-3 py-2 bg-slate-900 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- Trailer 02 -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-cyan-500/20 text-cyan-400 text-xs font-bold flex items-center justify-center font-mono">02</span>
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Trailer 02</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Title / Caption</label>
                            <input type="text" name="home_trailer_2_title" value="{{ $settings['home_trailer_2_title'] ?? 'Chapter II: Shadows & Blades' }}"
                                class="w-full px-3 py-2 bg-slate-900 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">YouTube Embed URL</label>
                            <input type="url" name="home_trailer_2_url" value="{{ $settings['home_trailer_2_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ' }}"
                                class="w-full px-3 py-2 bg-slate-900 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- Trailer 03 -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-cyan-500/20 text-cyan-400 text-xs font-bold flex items-center justify-center font-mono">03</span>
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Trailer 03</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Title / Caption</label>
                            <input type="text" name="home_trailer_3_title" value="{{ $settings['home_trailer_3_title'] ?? 'Chapter III: War of Emperium' }}"
                                class="w-full px-3 py-2 bg-slate-900 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">YouTube Embed URL</label>
                            <input type="url" name="home_trailer_3_url" value="{{ $settings['home_trailer_3_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ' }}"
                                class="w-full px-3 py-2 bg-slate-900 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: HOME 6 FEATURES -->
        <div x-show="activeTab === 'features'" class="space-y-6">
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4 text-cyan-400"></i>
                        <span>Features Section Header</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">The introduction block preceding the 6 hallmark feature cards.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Section Tag / Overline</label>
                        <input type="text" name="home_features_tag" value="{{ $settings['home_features_tag'] ?? 'Uncompromising Craftsmanship' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Section Title</label>
                        <input type="text" name="home_features_title" value="{{ $settings['home_features_title'] ?? 'Engineered For Champions' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Section Description</label>
                    <textarea name="home_features_desc" rows="2"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">{{ $settings['home_features_desc'] ?? 'Discover the hallmark features that establish our realm as the pinnacle of Ragnarok Online private servers.' }}</textarea>
                </div>
            </div>

            <!-- The 6 Feature Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Card 1 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Feature Card #1</span>
                        <div class="w-32">
                            <input type="text" name="home_feature_1_icon" value="{{ $settings['home_feature_1_icon'] ?? 'swords' }}"
                                placeholder="Icon (e.g. swords)"
                                class="w-full px-2.5 py-1 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-lg text-xs text-slate-200 text-right">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Title</label>
                        <input type="text" name="home_feature_1_title" value="{{ $settings['home_feature_1_title'] ?? 'Balanced Competitive PvP' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Description</label>
                        <textarea name="home_feature_1_desc" rows="3"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-300">{{ $settings['home_feature_1_desc'] ?? 'Meticulously tested skill formulas, item restrictions, and custom battlegrounds designed to ensure true mechanical skill triumphs over pay-to-win mechanics.' }}</textarea>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Feature Card #2</span>
                        <div class="w-32">
                            <input type="text" name="home_feature_2_icon" value="{{ $settings['home_feature_2_icon'] ?? 'castle' }}"
                                placeholder="Icon (e.g. castle)"
                                class="w-full px-2.5 py-1 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-lg text-xs text-slate-200 text-right">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Title</label>
                        <input type="text" name="home_feature_2_title" value="{{ $settings['home_feature_2_title'] ?? 'Prestigious War of Emperium' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Description</label>
                        <textarea name="home_feature_2_desc" rows="3"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-300">{{ $settings['home_feature_2_desc'] ?? 'Weekly castle sieges with automated guild economy payouts, custom castle treasure chests, and live guild rankings honoring sovereign alliances.' }}</textarea>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Feature Card #3</span>
                        <div class="w-32">
                            <input type="text" name="home_feature_3_icon" value="{{ $settings['home_feature_3_icon'] ?? 'crown' }}"
                                placeholder="Icon (e.g. crown)"
                                class="w-full px-2.5 py-1 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-lg text-xs text-slate-200 text-right">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Title</label>
                        <input type="text" name="home_feature_3_title" value="{{ $settings['home_feature_3_title'] ?? 'Custom End-Game Dungeons' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Description</label>
                        <textarea name="home_feature_3_desc" rows="3"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-300">{{ $settings['home_feature_3_desc'] ?? 'Face terrifying world bosses, mythic tier instance challenges, and exclusive costume craftables that test even the most organized parties.' }}</textarea>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Feature Card #4</span>
                        <div class="w-32">
                            <input type="text" name="home_feature_4_icon" value="{{ $settings['home_feature_4_icon'] ?? 'database' }}"
                                placeholder="Icon (e.g. database)"
                                class="w-full px-2.5 py-1 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-lg text-xs text-slate-200 text-right">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Title</label>
                        <input type="text" name="home_feature_4_title" value="{{ $settings['home_feature_4_title'] ?? 'Integrated Item Database' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Description</label>
                        <textarea name="home_feature_4_desc" rows="3"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-300">{{ $settings['home_feature_4_desc'] ?? 'Search item stats, drop rates, card effects, and equipment bonuses in real-time straight from our blazing-fast control panel.' }}</textarea>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Feature Card #5</span>
                        <div class="w-32">
                            <input type="text" name="home_feature_5_icon" value="{{ $settings['home_feature_5_icon'] ?? 'shield' }}"
                                placeholder="Icon (e.g. shield)"
                                class="w-full px-2.5 py-1 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-lg text-xs text-slate-200 text-right">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Title</label>
                        <input type="text" name="home_feature_5_title" value="{{ $settings['home_feature_5_title'] ?? 'Flawless Anti-Cheat Defense' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Description</label>
                        <textarea name="home_feature_5_desc" rows="3"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-300">{{ $settings['home_feature_5_desc'] ?? 'State-of-the-art packet verification, macro blockers, and dedicated game master surveillance to guarantee a level playing field for all.' }}</textarea>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Feature Card #6</span>
                        <div class="w-32">
                            <input type="text" name="home_feature_6_icon" value="{{ $settings['home_feature_6_icon'] ?? 'heart-handshake' }}"
                                placeholder="Icon (e.g. heart-handshake)"
                                class="w-full px-2.5 py-1 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-lg text-xs text-slate-200 text-right">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Title</label>
                        <input type="text" name="home_feature_6_title" value="{{ $settings['home_feature_6_title'] ?? 'Thriving Global Community' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Description</label>
                        <textarea name="home_feature_6_desc" rows="3"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-300">{{ $settings['home_feature_6_desc'] ?? 'Active Discord community, scheduled community mini-games, and responsive 24/7 staff support ready to assist your journey at any time.' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: TELEMETRY & CTA BANNER -->
        <div x-show="activeTab === 'telemetry_cta'" class="space-y-6">
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4 text-cyan-400"></i>
                        <span>Live Telemetry Panel Customizer</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Title, description, and anti-cheat status label on the telemetry strip.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Telemetry Section Title</label>
                        <input type="text" name="home_telemetry_title" value="{{ $settings['home_telemetry_title'] ?? 'Live Realm Telemetry' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Gepard Shield Badge Label</label>
                        <input type="text" name="home_gepard_status" value="{{ $settings['home_gepard_status'] ?? 'ACTIVE (v3.0)' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Telemetry Description</label>
                    <input type="text" name="home_telemetry_desc" value="{{ $settings['home_telemetry_desc'] ?? 'Real-time status for server daemons, world instances, and active adventurers.' }}"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                </div>
            </div>

            <!-- Bottom CTA Banner -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="bell" class="w-4 h-4 text-cyan-400"></i>
                        <span>Bottom Call-To-Action (CTA) Banner</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">The high-impact conversion banner located above the footer on the home page.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">CTA Overline / Badge</label>
                        <input type="text" name="home_cta_badge" value="{{ $settings['home_cta_badge'] ?? 'Join The Sovereign Order' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">CTA Main Title</label>
                        <input type="text" name="home_cta_title" value="{{ $settings['home_cta_title'] ?? 'Your Legacy Awaits In The Lost Realms' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">CTA Body Paragraph</label>
                    <textarea name="home_cta_desc" rows="3"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">{{ $settings['home_cta_desc'] ?? 'Download the complete high-resolution game client, forge your guild, and step into the most refined Ragnarok Online private server experience today.' }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Button 1 Text (Register)</label>
                        <input type="text" name="home_cta_btn1_text" value="{{ $settings['home_cta_btn1_text'] ?? 'Create Account' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Button 2 Text (Download)</label>
                        <input type="text" name="home_cta_btn2_text" value="{{ $settings['home_cta_btn2_text'] ?? 'Download Client' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>
            </div>

            <!-- News & Hall of Fame Headers -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-4">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="newspaper" class="w-4 h-4 text-cyan-400"></i>
                        <span>Section Titles: News &amp; Hall of Fame</span>
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">News Section Title</label>
                        <input type="text" name="home_news_title" value="{{ $settings['home_news_title'] ?? 'Latest Realm Chronicles' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Hall of Fame Section Title</label>
                        <input type="text" name="home_halloffame_title" value="{{ $settings['home_halloffame_title'] ?? 'Hall of Fame' }}"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: COMMUNITY & SOCIALS -->
        <div x-show="activeTab === 'social'" class="space-y-6">
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="share-2" class="w-4 h-4 text-cyan-400"></i>
                        <span>Social Media Channels &amp; Community Links</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Links rendered in the hero rate strip, quick access menus, and website footer.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Discord Server Invite URL</label>
                        <input type="url" name="discord_url" value="{{ $settings['discord_url'] ?? '' }}"
                            placeholder="https://discord.gg/your-invite"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Facebook Page / Group URL</label>
                        <input type="url" name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}"
                            placeholder="https://facebook.com/your-server"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">YouTube Channel URL</label>
                        <input type="url" name="youtube_url" value="{{ $settings['youtube_url'] ?? '' }}"
                            placeholder="https://youtube.com/@your-channel"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Twitter / X URL</label>
                        <input type="url" name="twitter_url" value="{{ $settings['twitter_url'] ?? '' }}"
                            placeholder="https://x.com/your-handle"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 6: PAYPAL GATEWAY -->
        <div x-show="activeTab === 'paypal'" class="space-y-6">
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">PayPal Gateway &amp; Instant In-Game Fulfillment</h3>
                        <p class="text-[11px] text-slate-400">Automate cash points (#CASHPOINTS) and storage item rewards when players pay via PayPal.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">PayPal Merchant Email</label>
                        <input type="email" name="paypal_email" value="{{ $settings['paypal_email'] ?? '' }}"
                            placeholder="your-paypal-business@example.com"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        <p class="text-[10px] text-slate-500 mt-1">Payments made on the donation page will be sent directly to this address.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">PayPal Gateway Status</label>
                        <select name="paypal_enabled" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                            <option value="1" {{ ($settings['paypal_enabled'] ?? '1') == '1' ? 'selected' : '' }}>Enabled (Accept PayPal Payments)</option>
                            <option value="0" {{ ($settings['paypal_enabled'] ?? '1') == '0' ? 'selected' : '' }}>Disabled (Manual Payments Only)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Environment Mode</label>
                        <select name="paypal_sandbox" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                            <option value="1" {{ ($settings['paypal_sandbox'] ?? '1') == '1' ? 'selected' : '' }}>Sandbox Mode (Testing / Simulation)</option>
                            <option value="0" {{ ($settings['paypal_sandbox'] ?? '1') == '0' ? 'selected' : '' }}>Live Production (Real Money)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Currency Code</label>
                        <input type="text" name="paypal_currency" value="{{ $settings['paypal_currency'] ?? 'USD' }}"
                            placeholder="USD" maxlength="3"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 uppercase">
                    </div>
                </div>

                <div class="p-4 bg-slate-950/70 border border-slate-800 rounded-xl text-xs text-slate-400 space-y-1.5">
                    <span class="font-bold text-cyan-400 flex items-center gap-1.5">
                        <i data-lucide="webhook" class="w-4 h-4"></i>
                        <span>Instant Payment Notification (IPN) Webhook URL:</span>
                    </span>
                    <p class="font-mono text-xs text-slate-200 break-all select-all bg-slate-900 px-3 py-2 rounded-lg border border-slate-800">{{ url('/donations/paypal/ipn') }}</p>
                    <p class="text-[10px] text-slate-500">Configure this exact URL in your PayPal Account Settings &gt; Instant Payment Notification to automate reward delivery without staff intervention.</p>
                </div>
            </div>
        </div>

        <!-- TAB 7: AETHER CHRONICLES THEME SETTINGS -->
        <div x-show="activeTab === 'chronicles'" class="space-y-6">
            <div class="bg-slate-900/80 border border-amber-500/30 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-amber-400 flex items-center gap-2">
                            <i data-lucide="crown" class="w-4 h-4 text-amber-400"></i>
                            <span>Aether Chronicles Commercial Theme Customization</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Edit all headlines, promotional copy, server specifications, and promotional cards on the classic Ragnarok theme.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        Episode XIII Style
                    </span>
                </div>

                <!-- 1. Server Specs in Left Sidebar -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="server" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Sidebar Server Specifications</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Server Type</label>
                            <input type="text" name="chronicles_server_type" value="{{ $settings['chronicles_server_type'] ?? 'Pre-Renewal' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Episode Label</label>
                            <input type="text" name="chronicles_episode" value="{{ $settings['chronicles_episode'] ?? 'Classic XIII' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Emulator Name</label>
                            <input type="text" name="chronicles_emulator" value="{{ $settings['chronicles_emulator'] ?? 'rAthena' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- 2. Main Promotional Banner -->
                <div class="space-y-3 pt-3 border-t border-slate-800">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Top Main Promotional Banner</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Promo Badge</label>
                            <input type="text" name="chronicles_badge" value="{{ $settings['chronicles_badge'] ?? 'Episode XIII: Noghaltz' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Promo Headline</label>
                            <input type="text" name="chronicles_promo_title" value="{{ $settings['chronicles_promo_title'] ?? 'Experience the True Ragnarok!' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Promo Subtitle / Narrative</label>
                        <textarea name="chronicles_promo_desc" rows="2"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">{{ $settings['chronicles_promo_desc'] ?? 'Join thousands of players in an epic adventure across Rune-Midgard!' }}</textarea>
                    </div>
                </div>

                <!-- 3. Featured WoE Card -->
                <div class="space-y-3 pt-3 border-t border-slate-800">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Featured Right Sidebar Card (WoE)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Card Title</label>
                            <input type="text" name="chronicles_feat1_title" value="{{ $settings['chronicles_feat1_title'] ?? 'Epic WoE Battles' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Card Description</label>
                            <input type="text" name="chronicles_feat1_desc" value="{{ $settings['chronicles_feat1_desc'] ?? 'Fight for your guild glory!' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Card Target URL</label>
                            <input type="text" name="chronicles_feat1_link" value="{{ $settings['chronicles_feat1_link'] ?? '/woe' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Thumbnail URL</label>
                            <input type="text" name="chronicles_feat1_img" value="{{ $settings['chronicles_feat1_img'] ?? '' }}"
                                placeholder="/themes/aether-chronicles/assets/images/feat_woe_boss.jpg"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- 4. Screenshots Right Sidebar Card (Admin Managed) -->
                <div class="space-y-3 pt-3 border-t border-slate-800">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="image" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Screenshots Showcase Card (Right Sidebar)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Section Title</label>
                            <input type="text" name="chronicles_screenshot_title" value="{{ $settings['chronicles_screenshot_title'] ?? 'Screenshots' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Screenshot Image URL / Path</label>
                            <input type="text" name="chronicles_screenshot_img" value="{{ $settings['chronicles_screenshot_img'] ?? '' }}"
                                placeholder="e.g. /themes/aether-chronicles/assets/images/screenshot_prontera.jpg"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Gallery / Target URL</label>
                            <input type="text" name="chronicles_screenshot_url" value="{{ $settings['chronicles_screenshot_url'] ?? '/news' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- 5. Custom Graphics & Mascot -->
                <div class="space-y-3 pt-3 border-t border-slate-800">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="palette" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Custom Theme Artwork &amp; Mascot Overrides</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Header Banner Image URL</label>
                            <input type="text" name="chronicles_header_bg" value="{{ $settings['chronicles_header_bg'] ?? '' }}"
                                placeholder="Leave empty for default HD Castle & Knight Hero banner"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Center Promo Banner Image URL</label>
                            <input type="text" name="chronicles_promo_bg" value="{{ $settings['chronicles_promo_bg'] ?? '' }}"
                                placeholder="Leave empty for default clean Rogue Girl banner"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Server Status Angeling Mascot URL</label>
                            <input type="text" name="chronicles_angeling_img" value="{{ $settings['chronicles_angeling_img'] ?? '' }}"
                                placeholder="Leave empty for default Angeling status badge"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- 6. Bottom Adventure Banner & CTA -->
                <div class="space-y-3 pt-3 border-t border-slate-800">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="flag" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Bottom Adventure Strip &amp; CTA</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">CTA Headline</label>
                            <input type="text" name="chronicles_cta_title" value="{{ $settings['chronicles_cta_title'] ?? 'Join the Adventure Today!' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">CTA Subtitle</label>
                            <input type="text" name="chronicles_cta_desc" value="{{ $settings['chronicles_cta_desc'] ?? 'Be part of our growing Ragnarok community.' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">CTA Button Text</label>
                            <input type="text" name="chronicles_cta_btn_text" value="{{ $settings['chronicles_cta_btn_text'] ?? 'Play Now' }}"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl text-xs text-slate-200">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Button -->
        <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
            <p class="text-xs text-slate-500">All modified settings are instantly applied to the live public portal and home page.</p>
            <button type="submit" class="py-2.5 px-6 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save All Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
