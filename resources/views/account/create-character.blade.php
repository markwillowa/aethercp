@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6"
     x-data="{
         step: 1,
         maxReachedStep: 1,
         stepError: '',
         direction: 0,
         name: @js(old('name', '')),
         gender: @js(old('gender', $accountSex)),
         hairStyle: {{ (int) old('hair_style', $settings['default_hairstyle']) }},
         hairColor: {{ (int) old('hair_color', $settings['default_hair_color']) }},
         jobId: {{ (int) old('job_id', $availableJobs[0]['id'] ?? 0) }},
         baseLevel: {{ (int) old('base_level', $settings['starting_base_level']) }},
         jobLevel: {{ (int) old('job_level', $settings['starting_job_level']) }},
         minNameLen: {{ (int) $settings['min_name_length'] }},
         maxNameLen: {{ (int) $settings['max_name_length'] }},
         allowSpaces: {{ $settings['allow_spaces_in_name'] ? 'true' : 'false' }},
         forbiddenNames: @js(array_values(array_filter(array_map(fn($v) => strtolower(trim($v)), explode(',', $settings['forbidden_names']))))),
         maxBaseLevel: {{ (int) $settings['max_base_level'] }},
         maxJobLevel: {{ (int) $settings['max_job_level'] }},
         jobs: @js($availableJobs),
         hairColors: @js($hairColors),
         proxyBaseUrl: @js(route('account.characters.sprite-preview')),
         hairProxyBaseUrl: @js(route('account.characters.hair-preview')),
         previewSpriteSrc: '',
         previewFailed: false,
         _debounceTimer: null,
         init() {
             this.refreshPreviewSprite();
             this.$watch('gender', () => this.schedulePreview());
             this.$watch('jobId', () => this.schedulePreview());
             this.$watch('hairStyle', () => this.schedulePreview());
             this.$watch('hairColor', () => this.schedulePreview());
         },
         schedulePreview() {
             clearTimeout(this._debounceTimer);
             this._debounceTimer = setTimeout(() => this.refreshPreviewSprite(), 80);
         },
         refreshPreviewSprite() {
             this.previewFailed = false;
             this.previewSpriteSrc = `${this.proxyBaseUrl}?gender=${this.gender}&job=${this.jobId}&hair=${this.hairStyle}&color=${this.hairColor}&dir=0`;
         },
         hairIconUrl(style) {
             return `${this.hairProxyBaseUrl}?gender=${this.gender}&hair=${style}`;
         },
         get selectedJob() {
             return this.jobs.find(j => Number(j.id) === Number(this.jobId)) || this.jobs[0] || { id: 0, name: 'Novice', sprite_url: null };
         },
         get selectedHairColor() {
             return this.hairColors[this.hairColor] || { name: 'Default', hex: '#e8c468' };
         },
         rotateLeft() {
             this.direction = (this.direction + 7) % 8;
         },
         rotateRight() {
             this.direction = (this.direction + 1) % 8;
         },
         validateStep(s) {
             this.stepError = '';
             if (s === 1) {
                 const trimmed = (this.name || '').trim();
                 if (trimmed.length < this.minNameLen || trimmed.length > this.maxNameLen) {
                     this.stepError = `Character name must be between ${this.minNameLen} and ${this.maxNameLen} characters before proceeding.`;
                     return false;
                 }
                 const rx = this.allowSpaces ? /^[a-zA-Z0-9_ ]+$/ : /^[a-zA-Z0-9_]+$/;
                 if (!rx.test(trimmed)) {
                     this.stepError = 'Character name contains invalid characters. Only letters, numbers, and underscores are allowed.';
                     return false;
                 }
                 const lower = trimmed.toLowerCase();
                 if (this.forbiddenNames.some(bad => bad && lower.includes(bad))) {
                     this.stepError = 'This character name is reserved or not allowed.';
                     return false;
                 }
                 if (!['M', 'F'].includes(this.gender)) {
                     this.stepError = 'Please select a valid gender.';
                     return false;
                 }
                 return true;
             }
             if (s === 2) {
                 if (!this.jobs.find(j => Number(j.id) === Number(this.jobId))) {
                     this.stepError = 'Please select an available starting job before proceeding.';
                     return false;
                 }
                 return true;
             }
             if (s === 3) {
                 const b = Number(this.baseLevel);
                 const j = Number(this.jobLevel);
                 if (!Number.isInteger(b) || b < 1 || b > this.maxBaseLevel) {
                     this.stepError = `Base level must be between 1 and ${this.maxBaseLevel}.`;
                     return false;
                 }
                 if (!Number.isInteger(j) || j < 1 || j > this.maxJobLevel) {
                     this.stepError = `Job level must be between 1 and ${this.maxJobLevel}.`;
                     return false;
                 }
                 return true;
             }
             return true;
         },
         goToStep(target) {
             if (target > this.maxReachedStep) {
                 return;
             }
             this.stepError = '';
             this.step = target;
         },
         nextStep() {
             if (this.validateStep(this.step) && this.step < 4) {
                 this.step++;
                 if (this.step > this.maxReachedStep) {
                     this.maxReachedStep = this.step;
                 }
             }
         }
     }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                Online Character Creation
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">Create New Adventurer</h1>
            <p class="text-xs text-slate-400 mt-1">
                Account #{{ $user->account_id }} • Slots Used: <strong class="text-cyan-400">{{ $existingCount }} / {{ $accountMaxSlots }}</strong>
            </p>
        </div>
        <a href="{{ route('account.characters') }}" class="text-xs text-slate-400 hover:text-white inline-flex items-center gap-1.5 font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to My Characters</span>
        </a>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($existingCount >= $accountMaxSlots)
        <div class="p-6 rounded-2xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-sm">
            You have reached the maximum character slot limit ({{ $existingCount }}/{{ $accountMaxSlots }}). Delete an existing character before creating a new one.
        </div>
    @elseif(empty($availableJobs))
        <div class="p-6 rounded-2xl bg-rose-950/40 border border-rose-500/40 text-rose-200 text-sm">
            No starting jobs are currently enabled by the administrator.
        </div>
    @else
        <!-- Step Progress Indicator (Future steps locked until Next Step is clicked) -->
        <div class="grid grid-cols-4 gap-2">
            @foreach([1 => '1. Appearance', 2 => '2. Starting Job', 3 => '3. Starting Level', 4 => '4. Confirm'] as $sNum => $sTitle)
                <button type="button"
                        @click="goToStep({{ $sNum }})"
                        :disabled="{{ $sNum }} > maxReachedStep"
                        :class="step === {{ $sNum }}
                            ? 'bg-cyan-500/20 border-cyan-500/60 text-cyan-300 cursor-pointer'
                            : ({{ $sNum }} <= maxReachedStep
                                ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300 cursor-pointer hover:bg-emerald-500/15'
                                : 'bg-slate-900/70 border-slate-800 text-slate-500 opacity-50 cursor-not-allowed')"
                        class="p-3 rounded-xl border text-xs font-bold text-center transition-all">
                    {{ $sTitle }}
                </button>
            @endforeach
        </div>

        <!-- Inline Step Validation Alert -->
        <div x-show="stepError" x-transition style="display: none;"
             class="p-3.5 rounded-2xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400 shrink-0"></i>
            <span x-text="stepError"></span>
        </div>

        <form method="POST" action="{{ route('account.characters.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf

            <!-- Left Column: RO-Style Live Character Preview Stage -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 space-y-4 h-fit">
                <!-- Gender Quick Toggle Header (RO Style) -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Character Preview</span>
                    @if($canChangeAccountGender)
                        <div class="inline-flex rounded-lg bg-slate-950 border border-slate-800 p-0.5 text-xs font-bold">
                            <button type="button" @click="gender = 'M'"
                                    class="px-2.5 py-0.5 rounded-md transition-colors cursor-pointer"
                                    :class="gender === 'M' ? 'bg-cyan-500/25 text-cyan-300' : 'text-slate-500 hover:text-slate-300'">♂ M</button>
                            <button type="button" @click="gender = 'F'"
                                    class="px-2.5 py-0.5 rounded-md transition-colors cursor-pointer"
                                    :class="gender === 'F' ? 'bg-pink-500/25 text-pink-300' : 'text-slate-500 hover:text-slate-300'">♀ F</button>
                        </div>
                    @else
                        <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-slate-950 border border-slate-800 text-slate-300">
                            {{ $accountSex === 'F' ? '♀ Female' : '♂ Male' }}
                        </span>
                    @endif
                </div>

                <!-- Stage with Actual RO Character Sprite -->
                <div class="relative mx-auto rounded-2xl bg-gradient-to-b from-slate-950 via-slate-900/90 to-slate-950 border border-slate-800 p-5 flex items-center justify-center shadow-inner">
                    <div class="flex flex-col items-center text-center space-y-2 min-w-0 px-2">
                        <div class="relative w-28 h-32 flex items-center justify-center">
                            <img x-show="previewSpriteSrc && !previewFailed"
                                 :src="previewSpriteSrc"
                                 :alt="selectedJob.name"
                                 class="max-w-full max-h-full object-contain drop-shadow-[0_6px_12px_rgba(0,0,0,0.65)] scale-125"
                                 x-on:error="previewFailed = true">
                            <div x-show="!previewSpriteSrc || previewFailed" style="display: none;" class="flex flex-col items-center justify-center gap-1">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center border border-white/20 text-lg font-black text-white"
                                     :style="`background: radial-gradient(circle at 35% 30%, ${selectedHairColor.hex}, #0f172a 95%);`"
                                     x-text="gender === 'F' ? '♀' : '♂'"></div>
                                <span class="text-[10px] font-bold text-cyan-300" x-text="selectedJob.name"></span>
                            </div>
                        </div>

                        <div class="px-2.5 py-0.5 rounded-full bg-slate-950/90 border border-slate-700/80 text-[11px] font-bold text-cyan-300"
                             x-text="selectedJob.name"></div>
                        <p class="text-sm font-black text-white truncate max-w-[160px]" x-text="name || 'Unnamed Adventurer'"></p>
                    </div>
                </div>

                <!-- Live Summary Stats -->
                <div class="space-y-2 text-xs bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Gender</span>
                        <span class="font-bold" :class="gender === 'F' ? 'text-pink-300' : 'text-cyan-300'" x-text="gender === 'F' ? 'Female (♀)' : 'Male (♂)'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Starting Job</span>
                        <span class="font-bold text-emerald-400" x-text="selectedJob.name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Hairstyle</span>
                        <span class="font-mono font-bold text-cyan-300" x-text="(gender === 'F' ? 'Female' : 'Male') + ' Style #' + hairStyle"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Hair Color</span>
                        <span class="inline-flex items-center gap-1.5 font-semibold text-white">
                            <span class="w-3 h-3 rounded-full border border-white/40" :style="`background-color: ${selectedHairColor.hex};`"></span>
                            <span x-text="selectedHairColor.name"></span>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Starting Level</span>
                        <span class="font-mono font-bold text-amber-300" x-text="'Base ' + baseLevel + ' / Job ' + jobLevel"></span>
                    </div>
                </div>
            </div>

            <!-- Right 2 Columns: 4-Step Wizard -->
            <div class="lg:col-span-2 bg-slate-900/80 border border-slate-800 rounded-3xl p-6 space-y-6">

                <!-- Step 1: Character Appearance -->
                <div x-show="step === 1" class="space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-white">Step 1 — Character Appearance</h2>
                        <p class="text-xs text-slate-400">Enter your character name and customize gender, hairstyle, and hair color.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Character Name</label>
                        <input type="text" name="name" x-model="name"
                               minlength="{{ $settings['min_name_length'] }}" maxlength="{{ $settings['max_name_length'] }}"
                               placeholder="Enter character name ({{ $settings['min_name_length'] }}–{{ $settings['max_name_length'] }} chars)"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:border-cyan-500 focus:outline-none" required>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Gender</label>
                        @if($canChangeAccountGender)
                            <div class="grid grid-cols-2 gap-3">
                                <label @click="gender = 'M'" :class="gender === 'M' ? 'bg-cyan-500/20 border-cyan-500/60 text-white' : 'bg-slate-950/60 border-slate-800 text-slate-400'" class="p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 cursor-pointer transition-all">
                                    <input type="radio" name="gender" value="M" x-model="gender" class="hidden">
                                    <span class="text-base text-cyan-400">♂</span>
                                    <span>Male</span>
                                </label>
                                <label @click="gender = 'F'" :class="gender === 'F' ? 'bg-pink-500/20 border-pink-500/60 text-white' : 'bg-slate-950/60 border-slate-800 text-slate-400'" class="p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 cursor-pointer transition-all">
                                    <input type="radio" name="gender" value="F" x-model="gender" class="hidden">
                                    <span class="text-base text-pink-400">♀</span>
                                    <span>Female</span>
                                </label>
                            </div>
                            @if($existingCount > 0)
                                <p class="text-[11px] text-slate-500 mt-1.5">
                                    Note: rAthena stores gender at the account level (<code class="text-cyan-400">login.sex</code>). Changing gender updates your account's gender for new characters.
                                </p>
                            @endif
                        @else
                            <input type="hidden" name="gender" value="{{ $accountSex }}">
                            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-300">
                                Account Gender: <strong class="text-cyan-400">{{ $accountSex === 'F' ? 'Female (♀)' : 'Male (♂)' }}</strong>
                                <span class="text-slate-500 block mt-0.5">Gender selection is disabled by the server administrator.</span>
                            </div>
                        @endif
                    </div>

                    <!-- Hair Style Grid (Actual RO Sprite Previews per Style + Gender + Hair Color) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-slate-300">
                                Hair Style (<span x-text="gender === 'F' ? 'Female' : 'Male'"></span>)
                            </label>
                            <span class="text-[11px] font-mono text-cyan-400" x-text="'Selected: #' + hairStyle"></span>
                        </div>
                        <div class="grid grid-cols-5 sm:grid-cols-7 gap-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($settings['hairstyles'] as $hStyle)
                                <label @click="hairStyle = {{ $hStyle }}"
                                       :class="Number(hairStyle) === {{ $hStyle }} ? 'bg-cyan-500/20 border-cyan-400 ring-2 ring-cyan-500/40 text-white' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700'"
                                       class="p-2 rounded-xl border flex flex-col items-center gap-1 cursor-pointer transition-all">
                                    <input type="radio" name="hair_style" value="{{ $hStyle }}" x-model="hairStyle" class="hidden">
                                    <div class="w-11 h-11 rounded-lg bg-slate-900/90 border border-slate-800 flex items-center justify-center overflow-hidden">
                                        <img :src="hairIconUrl({{ $hStyle }})"
                                             loading="lazy"
                                             alt="Style #{{ $hStyle }}"
                                             class="w-10 h-10 object-contain"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span style="display: none;" class="text-xs font-bold text-slate-300" x-text="gender === 'F' ? '♀' : '♂'"></span>
                                    </div>
                                    <span class="text-[10px] font-mono font-bold">#{{ $hStyle }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Hair Color Palette (RO Swatch Grid) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-slate-300">Hair Color</label>
                            <span class="text-[11px] text-slate-400" x-text="selectedHairColor.name"></span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($settings['hair_colors'] as $hColor)
                                @php $cInfo = $hairColors[$hColor] ?? ['name' => "Color #{$hColor}", 'hex' => '#94a3b8']; @endphp
                                <label @click.prevent="hairColor = {{ $hColor }}"
                                       :class="Number(hairColor) === {{ $hColor }} ? 'border-cyan-400 bg-slate-800 ring-2 ring-cyan-500/40' : 'border-slate-800 bg-slate-950/60'"
                                       class="px-3 py-2 rounded-xl border flex items-center gap-2 text-xs text-slate-200 cursor-pointer transition-all"
                                       title="{{ $cInfo['name'] }}">
                                    <input type="radio" name="hair_color" value="{{ $hColor }}" :checked="Number(hairColor) === {{ $hColor }}" class="hidden">
                                    <span class="w-5 h-5 rounded-md border border-white/40 shrink-0 shadow-sm" style="background-color: {{ $cInfo['hex'] }};"></span>
                                    <span class="text-[11px]">{{ $cInfo['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Step 2: Starting Job -->
                <div x-show="step === 2" style="display: none;" class="space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-white">Step 2 — Starting Job</h2>
                        <p class="text-xs text-slate-400">Select your starting class from the jobs enabled by the server administrator.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-80 overflow-y-auto pr-1">
                        @foreach($availableJobs as $job)
                            <label @click="jobId = {{ $job['id'] }}"
                                   :class="Number(jobId) === {{ $job['id'] }} ? 'bg-cyan-500/15 border-cyan-500 text-white shadow-md shadow-cyan-500/10' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:border-slate-700'"
                                   class="p-3.5 rounded-2xl border flex items-center gap-3 cursor-pointer transition-all">
                                <input type="radio" name="job_id" value="{{ $job['id'] }}" x-model="jobId" class="hidden">
                                <div class="w-11 h-11 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden p-1">
                                    <i data-lucide="sword" class="w-5 h-5 text-cyan-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold truncate">{{ $job['name'] }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $job['category_label'] }} • ID #{{ $job['id'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Step 3: Starting Level -->
                <div x-show="step === 3" style="display: none;" class="space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-white">Step 3 — Starting Level</h2>
                        <p class="text-xs text-slate-400">
                            {{ $settings['allow_custom_levels'] ? 'Customize your starting Base and Job levels within server limits.' : 'Starting levels are fixed by the server administrator.' }}
                        </p>
                    </div>

                    @if($settings['allow_custom_levels'])
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Base Level (1 – {{ $settings['max_base_level'] }})</label>
                                <input type="number" name="base_level" x-model="baseLevel" min="1" max="{{ $settings['max_base_level'] }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Job Level (1 – {{ $settings['max_job_level'] }})</label>
                                <input type="number" name="job_level" x-model="jobLevel" min="1" max="{{ $settings['max_job_level'] }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white">
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="base_level" value="{{ $settings['starting_base_level'] }}">
                        <input type="hidden" name="job_level" value="{{ $settings['starting_job_level'] }}">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                                <span class="text-xs text-slate-400 block">Fixed Base Level</span>
                                <span class="text-2xl font-black text-cyan-400">{{ $settings['starting_base_level'] }}</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                                <span class="text-xs text-slate-400 block">Fixed Job Level</span>
                                <span class="text-2xl font-black text-blue-400">{{ $settings['starting_job_level'] }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Step 4: Confirmation -->
                <div x-show="step === 4" style="display: none;" class="space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-white">Step 4 — Confirmation</h2>
                        <p class="text-xs text-slate-400">Review your new character details before creating it in the rAthena database.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-800/80">
                            <span class="text-slate-400">Character Name</span>
                            <span class="font-bold text-white" x-text="name || '(Not set)'"></span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-800/80">
                            <span class="text-slate-400">Gender</span>
                            <span class="font-bold text-white" x-text="gender === 'F' ? 'Female (♀)' : 'Male (♂)'"></span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-800/80">
                            <span class="text-slate-400">Appearance</span>
                            <span class="font-bold text-cyan-300" x-text="(gender === 'F' ? 'Female' : 'Male') + ' Style #' + hairStyle + ' • ' + selectedHairColor.name"></span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-800/80">
                            <span class="text-slate-400">Starting Job</span>
                            <span class="font-bold text-emerald-400" x-text="selectedJob.name"></span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-slate-400">Base / Job Level</span>
                            <span class="font-bold text-amber-300" x-text="'Lv. ' + baseLevel + ' / ' + jobLevel"></span>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3 rounded-xl text-sm font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg shadow-cyan-500/25 transition-all cursor-pointer flex items-center justify-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>Create Character</span>
                    </button>
                </div>

                <!-- Step Navigation -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <button type="button" x-show="step > 1" @click="stepError = ''; step--"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 cursor-pointer">
                        Previous
                    </button>
                    <div class="ml-auto">
                        <button type="button" x-show="step < 4" @click="nextStep()"
                                class="px-5 py-2 rounded-xl text-xs font-bold bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 cursor-pointer">
                            Next Step
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
