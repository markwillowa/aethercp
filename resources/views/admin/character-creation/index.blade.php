@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6" x-data="{ tab: 'general' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i data-lucide="user-plus" class="w-6 h-6 text-cyan-400"></i>
                <span>Character Creation Settings</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Configure Online Character Creation appearance, dynamic starting jobs, levels, presets, and starting equipment.
            </p>
        </div>
        <a href="{{ route('account.characters.create') }}" target="_blank" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-slate-700 inline-flex items-center gap-1.5 transition-colors shrink-0">
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            <span>Preview Player Creator</span>
        </a>
    </div>

    <!-- Quick Presets Bar -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                    <i data-lucide="wand-2" class="w-4 h-4"></i>
                    <span>Server Presets</span>
                </h2>
                <p class="text-[11px] text-slate-400 mt-0.5">Quickly load recommended starting jobs and level caps for your server type, then fine-tune any tab below.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @foreach($presets as $presetKey => $preset)
                <form method="POST" action="{{ route('admin.character-creation.preset') }}">
                    @csrf
                    <input type="hidden" name="preset" value="{{ $presetKey }}">
                    <button type="submit"
                            class="w-full text-left p-3 rounded-xl border transition-all h-full flex flex-col justify-between cursor-pointer {{ $settings['preset'] === $presetKey ? 'bg-cyan-500/15 border-cyan-500/50 text-white' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700 text-slate-300' }}">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold">{{ $preset['label'] }}</span>
                                @if($settings['preset'] === $presetKey)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-cyan-500/20 text-cyan-300">Active</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1 leading-relaxed">{{ $preset['description'] }}</p>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    <!-- Main Settings Form -->
    <form method="POST" action="{{ route('admin.character-creation.update') }}" class="space-y-6">
        @csrf

        <!-- Tabs Navigation -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
            @php
                $tabs = [
                    'general' => ['label' => 'General', 'icon' => 'sliders'],
                    'appearance' => ['label' => 'Appearance', 'icon' => 'palette'],
                    'jobs' => ['label' => 'Starting Jobs', 'icon' => 'swords'],
                    'levels' => ['label' => 'Levels', 'icon' => 'trending-up'],
                    'restrictions' => ['label' => 'Restrictions', 'icon' => 'shield-check'],
                    'equipment' => ['label' => 'Starting Equipment', 'icon' => 'backpack'],
                ];
            @endphp
            @foreach($tabs as $tKey => $tMeta)
                <button type="button"
                        @click="tab = '{{ $tKey }}'"
                        :class="tab === '{{ $tKey }}' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50' : 'bg-slate-900/70 text-slate-400 border-slate-800 hover:text-white'"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all inline-flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="{{ $tMeta['icon'] }}" class="w-3.5 h-3.5"></i>
                    <span>{{ $tMeta['label'] }}</span>
                </button>
            @endforeach
        </div>

        <!-- Tab 1: General -->
        <div x-show="tab === 'general'" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-5">
            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-white">Enable Online Character Creation</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Allow authenticated players to create new rAthena characters directly from the web control panel.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" class="sr-only peer" {{ $settings['enabled'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Starting Map</label>
                    <input type="text" name="start_map" value="{{ old('start_map', $settings['start_map']) }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Start X Coordinate</label>
                    <input type="number" name="start_x" value="{{ old('start_x', $settings['start_x']) }}" min="1" max="512" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Start Y Coordinate</label>
                    <input type="number" name="start_y" value="{{ old('start_y', $settings['start_y']) }}" min="1" max="512" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Starting Zeny</label>
                    <input type="number" name="start_zeny" value="{{ old('start_zeny', $settings['start_zeny']) }}" min="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
            </div>
        </div>

        <!-- Tab 2: Appearance -->
        <div x-show="tab === 'appearance'" style="display: none;" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-6">
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white">Enable Gender Selection</h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            In rAthena, gender is stored at the account level (<code class="text-cyan-400">login.sex</code>). Players with 0 existing characters can choose Male/Female; accounts with existing characters automatically inherit their account gender to prevent sprite mismatches.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-4">
                        <input type="checkbox" name="enable_gender" value="1" class="sr-only peer" {{ $settings['enable_gender'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Hairstyle ID</label>
                    <input type="number" name="default_hairstyle" value="{{ old('default_hairstyle', $settings['default_hairstyle']) }}" min="1" max="100" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Hair Color ID</label>
                    <select name="default_hair_color" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white">
                        @foreach($hairColors as $cId => $cMeta)
                            <option value="{{ $cId }}" {{ (int) $settings['default_hair_color'] === $cId ? 'selected' : '' }}>
                                #{{ $cId }} - {{ $cMeta['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Optional ROChargen Live Sprite URL Template</label>
                <input type="text" name="chargen_url" value="{{ old('chargen_url', $settings['chargen_url'] ?? '') }}"
                       placeholder="e.g. https://cp.example.com/chargen/avatar/{job}/{gender}/{hair}/{color}/{dir}"
                       class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs font-mono text-white">
                <p class="text-[11px] text-slate-500 mt-1">Supports placeholders: <code class="text-cyan-400">{job}</code>, <code class="text-cyan-400">{gender}</code>, <code class="text-cyan-400">{hair}</code>, <code class="text-cyan-400">{color}</code>, <code class="text-cyan-400">{dir}</code>. Leave blank to use built-in job icons &amp; hair swatches.</p>
            </div>

            <!-- Available Hairstyles -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Available Hairstyles (1–42)</label>
                <div class="grid grid-cols-6 sm:grid-cols-9 md:grid-cols-14 gap-2">
                    @for($h = 1; $h <= 42; $h++)
                        <label class="flex items-center justify-center gap-1.5 p-2 rounded-lg border text-xs cursor-pointer transition-colors {{ in_array($h, $settings['hairstyles'], true) ? 'bg-cyan-500/15 border-cyan-500/40 text-cyan-300' : 'bg-slate-950/60 border-slate-800 text-slate-400' }}">
                            <input type="checkbox" name="hairstyles[]" value="{{ $h }}" class="rounded border-slate-700 text-cyan-500 focus:ring-0" {{ in_array($h, $settings['hairstyles'], true) ? 'checked' : '' }}>
                            <span class="font-mono">{{ $h }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <!-- Available Hair Colors -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Available Hair Colors</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5">
                    @foreach($hairColors as $cId => $cMeta)
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl border text-xs cursor-pointer transition-colors {{ in_array($cId, $settings['hair_colors'], true) ? 'bg-cyan-500/15 border-cyan-500/40 text-white' : 'bg-slate-950/60 border-slate-800 text-slate-400' }}">
                            <input type="checkbox" name="hair_colors[]" value="{{ $cId }}" class="rounded border-slate-700 text-cyan-500 focus:ring-0" {{ in_array($cId, $settings['hair_colors'], true) ? 'checked' : '' }}>
                            <span class="w-4 h-4 rounded-full border border-white/30 shrink-0" style="background-color: {{ $cMeta['hex'] }};"></span>
                            <span class="truncate">#{{ $cId }} {{ $cMeta['name'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tab 3: Starting Jobs -->
        <div x-show="tab === 'jobs'" style="display: none;" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-6">
            <div class="p-3.5 rounded-xl bg-cyan-950/30 border border-cyan-500/30 text-xs text-cyan-200">
                Only jobs checked below (whose category is also enabled) will be visible to players during character creation.
            </div>

            <div class="space-y-4">
                @foreach($jobCatalog as $catKey => $cat)
                    @php
                        $catEnabled = in_array($catKey, $settings['enabled_categories'], true);
                    @endphp
                    <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-3" data-job-category>
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5 gap-3">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="enabled_categories[]" value="{{ $catKey }}"
                                       data-cat-checkbox
                                       class="rounded border-slate-700 text-cyan-500 focus:ring-0"
                                       {{ $catEnabled ? 'checked' : '' }}>
                                <div>
                                    <span class="text-sm font-bold text-white">{{ $cat['label'] }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $cat['description'] }}</span>
                                </div>
                            </label>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="aetherToggleJobCategory(this, true)"
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/30 cursor-pointer transition-colors">
                                    Select All
                                </button>
                                <button type="button" onclick="aetherToggleJobCategory(this, false)"
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 cursor-pointer transition-colors">
                                    Clear
                                </button>
                                <span class="text-[10px] font-mono text-slate-500 ml-1">{{ count($cat['jobs']) }} jobs</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
                            @foreach($cat['jobs'] as $jobId => $jobName)
                                <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-900/80 border border-slate-800 hover:border-slate-700 text-xs text-slate-200 cursor-pointer">
                                    <input type="checkbox" name="enabled_jobs[]" value="{{ $jobId }}"
                                           data-job-checkbox
                                           class="rounded border-slate-700 text-cyan-500 focus:ring-0"
                                           {{ in_array((int) $jobId, $settings['enabled_jobs'], true) ? 'checked' : '' }}>
                                    <span class="truncate">{{ $jobName }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono ml-auto">#{{ $jobId }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Custom Jobs -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-3">
                    <label class="flex items-center gap-2.5 cursor-pointer border-b border-slate-800 pb-2.5">
                        <input type="checkbox" name="enabled_categories[]" value="custom"
                               class="rounded border-slate-700 text-cyan-500 focus:ring-0"
                               {{ in_array('custom', $settings['enabled_categories'], true) ? 'checked' : '' }}>
                        <div>
                            <span class="text-sm font-bold text-white">Custom Jobs</span>
                            <span class="text-[11px] text-slate-400 block">Define custom server job IDs (one per line: <code class="text-cyan-400">job_id:Job Name</code>).</span>
                        </div>
                    </label>
                    @php
                        $customJobsText = collect($settings['custom_jobs'] ?? [])
                            ->map(fn ($cj) => ($cj['id'] ?? '') . ':' . ($cj['name'] ?? ''))
                            ->implode("\n");
                    @endphp
                    <textarea name="custom_jobs_raw" rows="3" placeholder="4244:Hyper Novice&#10;4259:Spirit Handler"
                              class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs font-mono text-white">{{ old('custom_jobs_raw', $customJobsText) }}</textarea>
                    @if(!empty($settings['custom_jobs']))
                        <div class="flex flex-wrap gap-2">
                            @foreach($settings['custom_jobs'] as $cj)
                                <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 cursor-pointer">
                                    <input type="checkbox" name="enabled_jobs[]" value="{{ $cj['id'] }}"
                                           class="rounded border-slate-700 text-cyan-500 focus:ring-0"
                                           {{ in_array((int) $cj['id'], $settings['enabled_jobs'], true) ? 'checked' : '' }}>
                                    <span>{{ $cj['name'] }} (#{{ $cj['id'] }})</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 4: Levels -->
        <div x-show="tab === 'levels'" style="display: none;" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Starting Base Level</label>
                    <input type="number" name="starting_base_level" value="{{ old('starting_base_level', $settings['starting_base_level']) }}" min="1" max="{{ $settings['server_max_base_level'] }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Starting Job Level</label>
                    <input type="number" name="starting_job_level" value="{{ old('starting_job_level', $settings['starting_job_level']) }}" min="1" max="{{ $settings['server_max_job_level'] }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Maximum Base Level (Cap: {{ $settings['server_max_base_level'] }})</label>
                    <input type="number" name="max_base_level" value="{{ old('max_base_level', $settings['max_base_level']) }}" min="1" max="{{ $settings['server_max_base_level'] }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Maximum Job Level (Cap: {{ $settings['server_max_job_level'] }})</label>
                    <input type="number" name="max_job_level" value="{{ old('max_job_level', $settings['max_job_level']) }}" min="1" max="{{ $settings['server_max_job_level'] }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-white">Allow Custom Starting Levels</h3>
                    <p class="text-xs text-slate-400 mt-0.5">If enabled, players can choose their starting Base/Job level up to the configured maximums above.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="allow_custom_levels" value="1" class="sr-only peer" {{ $settings['allow_custom_levels'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                </label>
            </div>
        </div>

        <!-- Tab 5: Restrictions -->
        <div x-show="tab === 'restrictions'" style="display: none;" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Max Character Slots per Account</label>
                    <input type="number" name="max_slots" value="{{ old('max_slots', $settings['max_slots']) }}" min="1" max="15" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Min Character Name Length</label>
                    <input type="number" name="min_name_length" value="{{ old('min_name_length', $settings['min_name_length']) }}" min="2" max="12" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Max Character Name Length</label>
                    <input type="number" name="max_name_length" value="{{ old('max_name_length', $settings['max_name_length']) }}" min="6" max="23" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Forbidden / Reserved Names (comma-separated)</label>
                <input type="text" name="forbidden_names" value="{{ old('forbidden_names', $settings['forbidden_names']) }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                    <div>
                        <h3 class="text-xs font-bold text-white">Allow Spaces in Character Name</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Enable if your rAthena server permits spaces in character names.</p>
                    </div>
                    <input type="checkbox" name="allow_spaces_in_name" value="1" class="rounded border-slate-700 text-cyan-500" {{ $settings['allow_spaces_in_name'] ? 'checked' : '' }}>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                    <div>
                        <h3 class="text-xs font-bold text-white">Require Account Offline</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Prevent creating characters while another character on the account is logged in.</p>
                    </div>
                    <input type="checkbox" name="require_offline" value="1" class="rounded border-slate-700 text-cyan-500" {{ $settings['require_offline'] ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        <!-- Tab 6: Starting Equipment -->
        <div x-show="tab === 'equipment'" style="display: none;" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-5">
            <div>
                <h3 class="text-sm font-bold text-white">Global Starting Items (All Jobs)</h3>
                <p class="text-xs text-slate-400 mt-0.5 mb-2">One item per line using <code class="text-cyan-400">item_id:quantity</code> (e.g. <code class="text-cyan-400">501:10</code> for 10x Red Potion).</p>
                @php
                    $globalItemsText = collect($settings['starting_items']['global'] ?? [])
                        ->map(fn ($i) => ($i['item_id'] ?? '') . ':' . ($i['amount'] ?? 1))
                        ->implode("\n");
                @endphp
                <textarea name="starting_items_global_raw" rows="4" placeholder="1201:1&#10;2301:1&#10;501:10"
                          class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs font-mono text-white">{{ old('starting_items_global_raw', $globalItemsText) }}</textarea>
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-3">
                <h3 class="text-sm font-bold text-white">Job-Specific Starting Items (Optional)</h3>
                <p class="text-xs text-slate-400">Assign extra starting equipment or consumables for specific enabled jobs.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach(app(\App\Services\Character\CharacterCreationService::class)->getAvailableJobs($settings) as $availJob)
                        @php
                            $jId = (string) $availJob['id'];
                            $jText = collect($settings['starting_items']['jobs'][$jId] ?? [])
                                ->map(fn ($i) => ($i['item_id'] ?? '') . ':' . ($i['amount'] ?? 1))
                                ->implode("\n");
                        @endphp
                        <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-white">{{ $availJob['name'] }}</span>
                                <span class="text-[10px] font-mono text-slate-500">Job #{{ $availJob['id'] }}</span>
                            </div>
                            <textarea name="starting_items_jobs[{{ $jId }}]" rows="2" placeholder="item_id:amount"
                                      class="w-full px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs font-mono text-white">{{ $jText }}</textarea>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg shadow-cyan-500/20 transition-all cursor-pointer">
                Save Character Creation Settings
            </button>
        </div>
    </form>
</div>
<script>
function aetherToggleJobCategory(btn, selectAll) {
    const box = btn.closest('[data-job-category]');
    if (!box) return;
    const catCb = box.querySelector('[data-cat-checkbox]');
    if (catCb) catCb.checked = !!selectAll;
    box.querySelectorAll('[data-job-checkbox]').forEach(cb => {
        cb.checked = !!selectAll;
    });
}
</script>
@endsection
