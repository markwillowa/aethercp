@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-6"
     x-data="{
         step: 1,
         maxReachedStep: 1,
         stepError: '',
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
         init() {
             this.refreshPreviewSprite();
             this.$watch('gender', () => this.refreshPreviewSprite());
             this.$watch('jobId', () => this.refreshPreviewSprite());
             this.$watch('hairStyle', () => this.refreshPreviewSprite());
             this.$watch('hairColor', () => this.refreshPreviewSprite());
         },
         refreshPreviewSprite() {
             this.previewFailed = false;
             this.previewSpriteSrc = `${this.proxyBaseUrl}?gender=${this.gender}&job=${this.jobId}&hair=${this.hairStyle}&color=${this.hairColor}&dir=0`;
         },
         hairIconUrl(style) { return `${this.hairProxyBaseUrl}?gender=${this.gender}&hair=${style}`; },
         get selectedJob() { return this.jobs.find(j => Number(j.id) === Number(this.jobId)) || this.jobs[0] || { id: 0, name: 'Novice' }; },
         get selectedHairColor() { return this.hairColors[this.hairColor] || { name: 'Default', hex: '#e8c468' }; },
         validateStep(s) {
             this.stepError = '';
             if (s === 1) {
                 const trimmed = (this.name || '').trim();
                 if (trimmed.length < this.minNameLen || trimmed.length > this.maxNameLen) {
                     this.stepError = `Character name must be between ${this.minNameLen} and ${this.maxNameLen} characters.`;
                     return false;
                 }
                 const rx = this.allowSpaces ? /^[a-zA-Z0-9_ ]+$/ : /^[a-zA-Z0-9_]+$/;
                 if (!rx.test(trimmed)) {
                     this.stepError = 'Character name contains invalid characters.';
                     return false;
                 }
                 const lower = trimmed.toLowerCase();
                 if (this.forbiddenNames.some(bad => bad && lower.includes(bad))) {
                     this.stepError = 'This character name is reserved or not allowed.';
                     return false;
                 }
             }
             return true;
         },
         goToStep(target) { if (target <= this.maxReachedStep) { this.stepError = ''; this.step = target; } },
         nextStep() { if (this.validateStep(this.step) && this.step < 4) { this.step++; if (this.step > this.maxReachedStep) this.maxReachedStep = this.step; } }
     }">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#D4AF37]/20 text-[#FFE4A0] border border-[#D4AF37]/40">Online Character Creation</span>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase mt-1">Create New Adventurer</h1>
            <p class="text-xs text-[#EAD2B0] mt-1">Account #{{ $user->account_id }} • Slots Used: <strong class="text-[#FFD700]">{{ $existingCount }} / {{ $accountMaxSlots }}</strong></p>
        </div>
        <a href="{{ route('account.characters') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>Adventurer Roster</span>
        </a>
    </div>

    @if($existingCount >= $accountMaxSlots)
        <div class="chronicles-panel-ivy text-xs text-[#663C00]">You have reached the maximum character slot limit ({{ $existingCount }}/{{ $accountMaxSlots }}).</div>
    @elseif(empty($availableJobs))
        <div class="chronicles-panel-ivy text-xs text-[#7B1F12]">No starting jobs are currently enabled by the administrator.</div>
    @else
        <!-- Step Progress -->
        <div class="grid grid-cols-4 gap-2">
            @foreach([1 => '1. Appearance', 2 => '2. Starting Job', 3 => '3. Starting Level', 4 => '4. Confirm'] as $sNum => $sTitle)
                <button type="button" @click="goToStep({{ $sNum }})" :disabled="{{ $sNum }} > maxReachedStep"
                        :class="step === {{ $sNum }} ? 'bg-[#4A2200] text-[#FFE4A0] border-[#D4AF37]' : ({{ $sNum }} <= maxReachedStep ? 'bg-[#EFE4D0] text-[#4A2200] border-[#8B5A2B]' : 'bg-[#FAF4EA] text-[#8C7A68] border-[#DECBB1] opacity-60 cursor-not-allowed')"
                        class="p-3 rounded-xl border text-xs font-cinzel font-bold text-center cursor-pointer">
                    {{ $sTitle }}
                </button>
            @endforeach
        </div>

        <div x-show="stepError" style="display: none;" class="p-3 rounded-xl bg-[#FCE8E6] border border-[#E6B0AA] text-[#7B1F12] text-xs" x-text="stepError"></div>

        <form method="POST" action="{{ route('account.characters.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf

            <!-- Left Preview -->
            <div class="chronicles-panel-ivy space-y-4 h-fit">
                <div class="flex items-center justify-between border-b border-[#DECBB1] pb-2">
                    <span class="font-cinzel text-xs font-bold uppercase text-[#4A2200]">Character Preview</span>
                    @if($canChangeAccountGender)
                        <div class="inline-flex rounded-lg bg-[#EFE4D0] border border-[#DECBB1] p-0.5 text-xs font-bold">
                            <button type="button" @click="gender = 'M'" :class="gender === 'M' ? 'bg-[#4A2200] text-[#FFE4A0]' : 'text-[#5A3D28]'" class="px-2.5 py-0.5 rounded cursor-pointer">♂ M</button>
                            <button type="button" @click="gender = 'F'" :class="gender === 'F' ? 'bg-[#9E2A5A] text-white' : 'text-[#5A3D28]'" class="px-2.5 py-0.5 rounded cursor-pointer">♀ F</button>
                        </div>
                    @endif
                </div>
                <div class="rounded-2xl bg-[#FAF4EA] border border-[#DECBB1] p-5 flex flex-col items-center text-center space-y-2 shadow-inner">
                    <div class="w-28 h-32 flex items-center justify-center">
                        <img x-show="previewSpriteSrc && !previewFailed" :src="previewSpriteSrc" :alt="selectedJob.name" class="max-w-full max-h-full object-contain scale-125" x-on:error="previewFailed = true">
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-[#4A2200] text-[#FFE4A0] text-[11px] font-bold" x-text="selectedJob.name"></span>
                    <p class="font-cinzel text-sm font-black text-[#4A2200]" x-text="name || 'Unnamed Adventurer'"></p>
                </div>
                <div class="space-y-1.5 text-xs text-[#5A3D28] bg-[#FAF4EA] p-3.5 rounded-xl border border-[#DECBB1]">
                    <div class="flex justify-between"><span>Gender</span><strong x-text="gender === 'F' ? 'Female (♀)' : 'Male (♂)'"></strong></div>
                    <div class="flex justify-between"><span>Starting Job</span><strong class="text-[#9E5600]" x-text="selectedJob.name"></strong></div>
                    <div class="flex justify-between"><span>Hair Style</span><strong x-text="'#' + hairStyle"></strong></div>
                    <div class="flex justify-between"><span>Hair Color</span><strong x-text="selectedHairColor.name"></strong></div>
                    <div class="flex justify-between"><span>Starting Level</span><strong x-text="'Base ' + baseLevel + ' / Job ' + jobLevel"></strong></div>
                </div>
            </div>

            <!-- Right Wizard -->
            <div class="lg:col-span-2 chronicles-panel-ivy space-y-5">
                <div x-show="step === 1" class="space-y-4">
                    <h2 class="font-cinzel text-lg font-bold text-[#4A2200]">Step 1 — Character Appearance</h2>
                    <div>
                        <label class="block text-xs font-bold text-[#4A2200] mb-1">Character Name</label>
                        <input type="text" name="name" x-model="name" minlength="{{ $settings['min_name_length'] }}" maxlength="{{ $settings['max_name_length'] }}" placeholder="Enter character name..." class="w-full px-4 py-2.5 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] text-sm text-[#4A2200]" required>
                    </div>
                    @if($canChangeAccountGender)
                        <div class="grid grid-cols-2 gap-3 text-xs font-bold">
                            <label @click="gender = 'M'" :class="gender === 'M' ? 'bg-[#4A2200] text-[#FFE4A0]' : 'bg-[#FAF4EA] text-[#5A3D28]'" class="p-3 rounded-xl border border-[#DECBB1] text-center cursor-pointer"><input type="radio" name="gender" value="M" x-model="gender" class="hidden">♂ Male</label>
                            <label @click="gender = 'F'" :class="gender === 'F' ? 'bg-[#9E2A5A] text-white' : 'bg-[#FAF4EA] text-[#5A3D28]'" class="p-3 rounded-xl border border-[#DECBB1] text-center cursor-pointer"><input type="radio" name="gender" value="F" x-model="gender" class="hidden">♀ Female</label>
                        </div>
                    @else
                        <input type="hidden" name="gender" value="{{ $accountSex }}">
                    @endif
                    <div class="grid grid-cols-5 sm:grid-cols-7 gap-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($settings['hairstyles'] as $hStyle)
                            <label @click="hairStyle = {{ $hStyle }}" :class="Number(hairStyle) === {{ $hStyle }} ? 'bg-[#4A2200] text-[#FFE4A0] border-[#D4AF37]' : 'bg-[#FAF4EA] border-[#DECBB1] text-[#4A2200]'" class="p-2 rounded-xl border flex flex-col items-center gap-1 cursor-pointer">
                                <input type="radio" name="hair_style" value="{{ $hStyle }}" x-model="hairStyle" class="hidden">
                                <img :src="hairIconUrl({{ $hStyle }})" loading="lazy" class="w-9 h-9 object-contain" onerror="this.style.display='none'">
                                <span class="text-[10px] font-mono">#{{ $hStyle }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($settings['hair_colors'] as $hColor)
                            @php $cInfo = $hairColors[$hColor] ?? ['name' => "Color #{$hColor}", 'hex' => '#94a3b8']; @endphp
                            <label @click.prevent="hairColor = {{ $hColor }}" :class="Number(hairColor) === {{ $hColor }} ? 'border-[#D4441D] bg-[#EFE4D0]' : 'border-[#DECBB1] bg-[#FAF4EA]'" class="px-2.5 py-1.5 rounded-xl border flex items-center gap-1.5 text-xs text-[#4A2200] cursor-pointer">
                                <input type="radio" name="hair_color" value="{{ $hColor }}" :checked="Number(hairColor) === {{ $hColor }}" class="hidden">
                                <span class="w-4 h-4 rounded border border-black/20" style="background-color: {{ $cInfo['hex'] }};"></span>
                                <span>{{ $cInfo['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="step === 2" style="display: none;" class="space-y-4">
                    <h2 class="font-cinzel text-lg font-bold text-[#4A2200]">Step 2 — Starting Job</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto pr-1">
                        @foreach($availableJobs as $job)
                            <label @click="jobId = {{ $job['id'] }}" :class="Number(jobId) === {{ $job['id'] }} ? 'bg-[#4A2200] text-[#FFE4A0] border-[#D4AF37]' : 'bg-[#FAF4EA] text-[#4A2200] border-[#DECBB1]'" class="p-3 rounded-xl border cursor-pointer flex items-center justify-between">
                                <input type="radio" name="job_id" value="{{ $job['id'] }}" x-model="jobId" class="hidden">
                                <span class="font-bold text-xs">{{ $job['name'] }}</span>
                                <span class="text-[10px] font-mono opacity-80">#{{ $job['id'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="step === 3" style="display: none;" class="space-y-4">
                    <h2 class="font-cinzel text-lg font-bold text-[#4A2200]">Step 3 — Starting Level</h2>
                    @if($settings['allow_custom_levels'])
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div><label class="block font-bold text-[#4A2200] mb-1">Base Level (1–{{ $settings['max_base_level'] }})</label><input type="number" name="base_level" x-model="baseLevel" min="1" max="{{ $settings['max_base_level'] }}" class="w-full px-3 py-2 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] text-[#4A2200]"></div>
                            <div><label class="block font-bold text-[#4A2200] mb-1">Job Level (1–{{ $settings['max_job_level'] }})</label><input type="number" name="job_level" x-model="jobLevel" min="1" max="{{ $settings['max_job_level'] }}" class="w-full px-3 py-2 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] text-[#4A2200]"></div>
                        </div>
                    @else
                        <input type="hidden" name="base_level" value="{{ $settings['starting_base_level'] }}">
                        <input type="hidden" name="job_level" value="{{ $settings['starting_job_level'] }}">
                        <p class="text-xs text-[#5A3D28]">Fixed Starting Levels: Base <strong>{{ $settings['starting_base_level'] }}</strong> / Job <strong>{{ $settings['starting_job_level'] }}</strong></p>
                    @endif
                </div>

                <div x-show="step === 4" style="display: none;" class="space-y-4">
                    <h2 class="font-cinzel text-lg font-bold text-[#4A2200]">Step 4 — Confirmation</h2>
                    <div class="p-4 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] space-y-2 text-xs text-[#4A2200]">
                        <div class="flex justify-between"><span>Name</span><strong x-text="name"></strong></div>
                        <div class="flex justify-between"><span>Job</span><strong x-text="selectedJob.name"></strong></div>
                        <div class="flex justify-between"><span>Level</span><strong x-text="'Lv. ' + baseLevel + ' / ' + jobLevel"></strong></div>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-[#4A2200] hover:bg-[#5E2C00] text-[#FFE4A0] border border-[#D4AF37] font-cinzel font-bold text-sm cursor-pointer">Create Character</button>
                </div>

                <div class="pt-4 border-t border-[#DECBB1] flex justify-between">
                    <button type="button" x-show="step > 1" @click="stepError = ''; step--" class="px-4 py-2 rounded-xl bg-[#EFE4D0] border border-[#DECBB1] text-xs font-bold text-[#4A2200] cursor-pointer">Previous</button>
                    <button type="button" x-show="step < 4" @click="nextStep()" class="ml-auto px-5 py-2 rounded-xl bg-[#4A2200] text-[#FFE4A0] text-xs font-bold cursor-pointer">Next Step</button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
