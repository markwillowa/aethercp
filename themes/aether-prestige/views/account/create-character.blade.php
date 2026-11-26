@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8"
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

    <!-- Prestige Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Create New Adventurer
            </h1>
            <p class="text-xs text-[#788394] mt-1">
                Account #{{ $user->account_id }} • Slots Used: <strong class="text-[#D8BE8A]">{{ $existingCount }} / {{ $accountMaxSlots }}</strong>
            </p>
        </div>
        <a href="{{ route('account.characters') }}" class="h-9 px-4 rounded-xl bg-[#151B26] border border-white/[0.08] text-xs font-bold text-[#AAB3C1] hover:text-white inline-flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4 text-[#C6A76A]"></i>
            <span>Back to My Characters</span>
        </a>
    </div>

    @if($existingCount >= $accountMaxSlots)
        <div class="p-6 rounded-2xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-sm">
            You have reached the maximum character slot limit ({{ $existingCount }}/{{ $accountMaxSlots }}). Delete an existing character before creating a new one.
        </div>
    @elseif(empty($availableJobs))
        <div class="p-6 rounded-2xl bg-rose-950/40 border border-rose-500/40 text-rose-200 text-sm">
            No starting jobs are currently enabled by the administrator.
        </div>
    @else
        <!-- Step Progress Indicator -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            @foreach([1 => '1. Appearance', 2 => '2. Starting Job', 3 => '3. Starting Level', 4 => '4. Confirm'] as $sNum => $sTitle)
                <button type="button"
                        @click="goToStep({{ $sNum }})"
                        :disabled="{{ $sNum }} > maxReachedStep"
                        :class="step === {{ $sNum }}
                            ? 'bg-[#C6A76A]/20 border-[#C6A76A]/60 text-[#D8BE8A] cursor-pointer'
                            : ({{ $sNum }} <= maxReachedStep
                                ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300 cursor-pointer hover:bg-emerald-500/15'
                                : 'bg-[#151B26] border-white/[0.08] text-[#788394] opacity-50 cursor-not-allowed')"
                        class="p-3 rounded-xl border text-xs font-cinzel font-bold uppercase tracking-wider text-center transition-all">
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

            <!-- Left Column: Live Character Preview Stage -->
            <div class="prestige-card p-6 space-y-4 h-fit">
                <div class="flex items-center justify-between border-b border-white/[0.08] pb-3">
                    <span class="font-cinzel text-[10px] font-bold uppercase tracking-widest text-[#788394]">Character Preview</span>
                    @if($canChangeAccountGender)
                        <div class="inline-flex rounded-lg bg-[#101725] border border-white/10 p-0.5 text-xs font-bold">
                            <button type="button" @click="gender = 'M'"
                                    class="px-2.5 py-0.5 rounded-md transition-colors cursor-pointer"
                                    :class="gender === 'M' ? 'bg-[#C6A76A]/25 text-[#D8BE8A]' : 'text-[#788394] hover:text-white'">♂ M</button>
                            <button type="button" @click="gender = 'F'"
                                    class="px-2.5 py-0.5 rounded-md transition-colors cursor-pointer"
                                    :class="gender === 'F' ? 'bg-pink-500/25 text-pink-300' : 'text-[#788394] hover:text-white'">♀ F</button>
                        </div>
                    @else
                        <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-[#101725] border border-white/10 text-[#D9DEE7]">
                            {{ $accountSex === 'F' ? '♀ Female' : '♂ Male' }}
                        </span>
                    @endif
                </div>

                <div class="relative mx-auto rounded-2xl bg-[#101725] border border-white/[0.08] p-5 flex items-center justify-center">
                    <div class="flex flex-col items-center text-center space-y-2 min-w-0 px-2">
                        <div class="relative w-28 h-32 flex items-center justify-center">
                            <img x-show="previewSpriteSrc && !previewFailed"
                                 :src="previewSpriteSrc"
                                 :alt="selectedJob.name"
                                 class="max-w-full max-h-full object-contain drop-shadow-[0_6px_12px_rgba(0,0,0,0.65)] scale-125"
                                 x-on:error="previewFailed = true">
                            <div x-show="!previewSpriteSrc || previewFailed" style="display: none;" class="flex flex-col items-center justify-center gap-1">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center border border-white/20 text-lg font-black text-white"
                                     :style="`background: radial-gradient(circle at 35% 30%, ${selectedHairColor.hex}, #101725 95%);`"
                                     x-text="gender === 'F' ? '♀' : '♂'"></div>
                                <span class="text-[10px] font-bold text-[#D8BE8A]" x-text="selectedJob.name"></span>
                            </div>
                        </div>

                        <div class="px-2.5 py-0.5 rounded-full bg-[#151B26] border border-[#C6A76A]/30 text-[11px] font-bold text-[#D8BE8A]"
                             x-text="selectedJob.name"></div>
                        <p class="font-cinzel text-sm font-bold text-white truncate max-w-[160px]" x-text="name || 'Unnamed Adventurer'"></p>
                    </div>
                </div>

                <div class="space-y-2 text-xs bg-[#101725] p-4 rounded-2xl border border-white/[0.08]">
                    <div class="flex justify-between">
                        <span class="text-[#788394]">Gender</span>
                        <span class="font-bold" :class="gender === 'F' ? 'text-pink-300' : 'text-[#D8BE8A]'" x-text="gender === 'F' ? 'Female (♀)' : 'Male (♂)'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#788394]">Starting Job</span>
                        <span class="font-bold text-emerald-400" x-text="selectedJob.name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#788394]">Hairstyle</span>
                        <span class="font-mono font-bold text-[#D8BE8A]" x-text="(gender === 'F' ? 'Female' : 'Male') + ' Style #' + hairStyle"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#788394]">Hair Color</span>
                        <span class="inline-flex items-center gap-1.5 font-semibold text-white">
                            <span class="w-3 h-3 rounded-full border border-white/40" :style="`background-color: ${selectedHairColor.hex};`"></span>
                            <span x-text="selectedHairColor.name"></span>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#788394]">Starting Level</span>
                        <span class="font-mono font-bold text-[#D8BE8A]" x-text="'Base ' + baseLevel + ' / Job ' + jobLevel"></span>
                    </div>
                </div>
            </div>

            <!-- Right 2 Columns: 4-Step Wizard -->
            <div class="lg:col-span-2 prestige-card p-6 space-y-6">

                <!-- Step 1: Character Appearance -->
                <div x-show="step === 1" class="space-y-5">
                    <div>
                        <h2 class="font-cinzel text-lg font-bold text-white uppercase tracking-wider">Step 1 — Character Appearance</h2>
                        <p class="text-xs text-[#788394]">Enter your character name and customize gender, hairstyle, and hair color.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#D9DEE7] mb-1.5">Character Name</label>
                        <input type="text" name="name" x-model="name"
                               minlength="{{ $settings['min_name_length'] }}" maxlength="{{ $settings['max_name_length'] }}"
                               placeholder="Enter character name ({{ $settings['min_name_length'] }}–{{ $settings['max_name_length'] }} chars)"
                               class="w-full px-4 py-2.5 rounded-xl bg-[#101725] border border-white/10 text-sm text-white focus:border-[#C6A76A] focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#D9DEE7] mb-1.5">Gender</label>
                        @if($canChangeAccountGender)
                            <div class="grid grid-cols-2 gap-3">
                                <label @click="gender = 'M'" :class="gender === 'M' ? 'bg-[#C6A76A]/20 border-[#C6A76A]/60 text-white' : 'bg-[#101725] border-white/10 text-[#AAB3C1]'" class="p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 cursor-pointer transition-all">
                                    <input type="radio" name="gender" value="M" x-model="gender" class="hidden">
                                    <span class="text-base text-[#D8BE8A]">♂</span>
                                    <span>Male</span>
                                </label>
                                <label @click="gender = 'F'" :class="gender === 'F' ? 'bg-pink-500/20 border-pink-500/60 text-white' : 'bg-[#101725] border-white/10 text-[#AAB3C1]'" class="p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 cursor-pointer transition-all">
                                    <input type="radio" name="gender" value="F" x-model="gender" class="hidden">
                                    <span class="text-base text-pink-400">♀</span>
                                    <span>Female</span>
                                </label>
                            </div>
                            @if($existingCount > 0)
                                <p class="text-[11px] text-[#788394] mt-1.5">
                                    Note: rAthena stores gender at the account level (<code class="text-[#D8BE8A]">login.sex</code>). Changing gender updates your account's gender for new characters.
                                </p>
                            @endif
                        @else
                            <input type="hidden" name="gender" value="{{ $accountSex }}">
                            <div class="p-3 rounded-xl bg-[#101725] border border-white/10 text-xs text-[#D9DEE7]">
                                Account Gender: <strong class="text-[#D8BE8A]">{{ $accountSex === 'F' ? 'Female (♀)' : 'Male (♂)' }}</strong>
                                <span class="text-[#788394] block mt-0.5">Gender selection is disabled by the server administrator.</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-[#D9DEE7]">
                                Hair Style (<span x-text="gender === 'F' ? 'Female' : 'Male'"></span>)
                            </label>
                            <span class="text-[11px] font-mono text-[#D8BE8A]" x-text="'Selected: #' + hairStyle"></span>
                        </div>
                        <div class="grid grid-cols-5 sm:grid-cols-7 gap-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($settings['hairstyles'] as $hStyle)
                                <label @click="hairStyle = {{ $hStyle }}"
                                       :class="Number(hairStyle) === {{ $hStyle }} ? 'bg-[#C6A76A]/20 border-[#C6A76A] ring-2 ring-[#C6A76A]/40 text-white' : 'bg-[#101725] border-white/10 text-[#AAB3C1] hover:border-white/25'"
                                       class="p-2 rounded-xl border flex flex-col items-center gap-1 cursor-pointer transition-all">
                                    <input type="radio" name="hair_style" value="{{ $hStyle }}" x-model="hairStyle" class="hidden">
                                    <div class="w-11 h-11 rounded-lg bg-[#151B26] border border-white/10 flex items-center justify-center overflow-hidden">
                                        <img :src="hairIconUrl({{ $hStyle }})"
                                             loading="lazy"
                                             alt="Style #{{ $hStyle }}"
                                             class="w-10 h-10 object-contain"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span style="display: none;" class="text-xs font-bold text-[#D9DEE7]" x-text="gender === 'F' ? '♀' : '♂'"></span>
                                    </div>
                                    <span class="text-[10px] font-mono font-bold">#{{ $hStyle }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-[#D9DEE7]">Hair Color</label>
                            <span class="text-[11px] text-[#788394]" x-text="selectedHairColor.name"></span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($settings['hair_colors'] as $hColor)
                                @php $cInfo = $hairColors[$hColor] ?? ['name' => "Color #{$hColor}", 'hex' => '#94a3b8']; @endphp
                                <label @click.prevent="hairColor = {{ $hColor }}"
                                       :class="Number(hairColor) === {{ $hColor }} ? 'border-[#C6A76A] bg-[#1B2432] ring-2 ring-[#C6A76A]/40' : 'border-white/10 bg-[#101725]'"
                                       class="px-3 py-2 rounded-xl border flex items-center gap-2 text-xs text-[#D9DEE7] cursor-pointer transition-all"
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
                        <h2 class="font-cinzel text-lg font-bold text-white uppercase tracking-wider">Step 2 — Starting Job</h2>
                        <p class="text-xs text-[#788394]">Select your starting class from the jobs enabled by the server administrator.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-80 overflow-y-auto pr-1">
                        @foreach($availableJobs as $job)
                            <label @click="jobId = {{ $job['id'] }}"
                                   :class="Number(jobId) === {{ $job['id'] }} ? 'bg-[#C6A76A]/15 border-[#C6A76A] text-white' : 'bg-[#101725] border-white/10 text-[#D9DEE7] hover:border-white/25'"
                                   class="p-3.5 rounded-2xl border flex items-center gap-3 cursor-pointer transition-all">
                                <input type="radio" name="job_id" value="{{ $job['id'] }}" x-model="jobId" class="hidden">
                                <div class="w-11 h-11 rounded-xl bg-[#151B26] border border-white/10 flex items-center justify-center shrink-0 p-1">
                                    <i data-lucide="sword" class="w-5 h-5 text-[#C6A76A]"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-cinzel text-sm font-bold truncate">{{ $job['name'] }}</p>
                                    <p class="text-[10px] text-[#788394]">{{ $job['category_label'] }} • ID #{{ $job['id'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Step 3: Starting Level -->
                <div x-show="step === 3" style="display: none;" class="space-y-5">
                    <div>
                        <h2 class="font-cinzel text-lg font-bold text-white uppercase tracking-wider">Step 3 — Starting Level</h2>
                        <p class="text-xs text-[#788394]">
                            {{ $settings['allow_custom_levels'] ? 'Customize your starting Base and Job levels within server limits.' : 'Starting levels are fixed by the server administrator.' }}
                        </p>
                    </div>

                    @if($settings['allow_custom_levels'])
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#D9DEE7] mb-1.5">Base Level (1 – {{ $settings['max_base_level'] }})</label>
                                <input type="number" name="base_level" x-model="baseLevel" min="1" max="{{ $settings['max_base_level'] }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-[#101725] border border-white/10 text-sm text-white focus:border-[#C6A76A] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#D9DEE7] mb-1.5">Job Level (1 – {{ $settings['max_job_level'] }})</label>
                                <input type="number" name="job_level" x-model="jobLevel" min="1" max="{{ $settings['max_job_level'] }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-[#101725] border border-white/10 text-sm text-white focus:border-[#C6A76A] focus:outline-none">
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="base_level" value="{{ $settings['starting_base_level'] }}">
                        <input type="hidden" name="job_level" value="{{ $settings['starting_job_level'] }}">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-[#101725] border border-white/10">
                                <span class="text-xs text-[#788394] block">Fixed Base Level</span>
                                <span class="text-2xl font-black text-[#D8BE8A]">{{ $settings['starting_base_level'] }}</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#101725] border border-white/10">
                                <span class="text-xs text-[#788394] block">Fixed Job Level</span>
                                <span class="text-2xl font-black text-[#D8BE8A]">{{ $settings['starting_job_level'] }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Step 4: Confirmation -->
                <div x-show="step === 4" style="display: none;" class="space-y-5">
                    <div>
                        <h2 class="font-cinzel text-lg font-bold text-white uppercase tracking-wider">Step 4 — Confirmation</h2>
                        <p class="text-xs text-[#788394]">Review your new character details before creating it in the rAthena database.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#101725] border border-white/10 space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-white/[0.06]">
                            <span class="text-[#788394]">Character Name</span>
                            <span class="font-bold text-white" x-text="name || '(Not set)'"></span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-white/[0.06]">
                            <span class="text-[#788394]">Gender</span>
                            <span class="font-bold text-white" x-text="gender === 'F' ? 'Female (♀)' : 'Male (♂)'"></span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-white/[0.06]">
                            <span class="text-[#788394]">Appearance</span>
                            <span class="font-bold text-[#D8BE8A]" x-text="(gender === 'F' ? 'Female' : 'Male') + ' Style #' + hairStyle + ' • ' + selectedHairColor.name"></span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-white/[0.06]">
                            <span class="text-[#788394]">Starting Job</span>
                            <span class="font-bold text-emerald-400" x-text="selectedJob.name"></span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-[#788394]">Base / Job Level</span>
                            <span class="font-bold text-[#D8BE8A]" x-text="'Lv. ' + baseLevel + ' / ' + jobLevel"></span>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3 rounded-xl text-sm font-cinzel font-bold uppercase tracking-wider bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] transition-all cursor-pointer flex items-center justify-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>Create Character</span>
                    </button>
                </div>

                <!-- Step Navigation -->
                <div class="pt-4 border-t border-white/[0.08] flex items-center justify-between">
                    <button type="button" x-show="step > 1" @click="stepError = ''; step--"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-[#101725] border border-white/10 text-[#AAB3C1] hover:text-white cursor-pointer">
                        Previous
                    </button>
                    <div class="ml-auto">
                        <button type="button" x-show="step < 4" @click="nextStep()"
                                class="px-5 py-2 rounded-xl text-xs font-cinzel font-bold uppercase tracking-wider bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] cursor-pointer">
                            Next Step
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
