@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ tab: 'profile' }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $character->name }}</h1>
            <p class="text-xs text-slate-400 mt-1">
                {{ $character->job_name }} • Base Lv. {{ $character->base_level }} / Job Lv. {{ $character->job_level }} •
                Cash Points: <span class="text-cyan-400 font-bold">{{ number_format($cash_points) }} CP</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase {{ (int)$character->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-300' }}">
                {{ (int)$character->online > 0 ? 'Online' : 'Offline' }}
            </span>
            <a href="{{ route('account.characters') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 inline-flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>My Characters</span>
            </a>
        </div>
    </div>

    @if((int)$character->online > 0)
        <div class="bg-amber-950/40 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-200 flex items-center gap-2.5">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400 shrink-0"></i>
            <span>This character is currently <strong>Online</strong>. Please log out of the game before using character services.</span>
        </div>
    @endif

    @if($active_lock)
        <div class="bg-rose-950/40 border border-rose-500/30 rounded-2xl p-4 text-xs text-rose-200 flex items-center justify-between gap-3">
            <div>
                <strong>Character Locked ({{ $active_lock->lock_scope }}):</strong> {{ $active_lock->reason }}
            </div>
            @if((int)$active_lock->locked_by === (int)auth()->id() && $active_lock->lock_scope === 'services')
                <form method="POST" action="{{ route('account.characters.services.lock', $character->char_id) }}">
                    @csrf
                    <input type="hidden" name="action" value="unlock">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-slate-950 font-bold cursor-pointer">Unlock</button>
                </form>
            @endif
        </div>
    @endif

    @if($deletion_request && $deletion_request->status === 'pending')
        <div class="bg-amber-950/40 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-200 flex items-center justify-between gap-3">
            <div>
                <strong>Scheduled for Deletion:</strong> {{ $deletion_request->scheduled_delete_at->format('M d, Y H:i') }}
            </div>
            <form method="POST" action="{{ route('account.characters.services.deletion.cancel', $character->char_id) }}">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-slate-950 font-bold cursor-pointer">Cancel Deletion</button>
            </form>
        </div>
    @endif

    <div class="flex flex-wrap gap-2 border-b border-slate-800 pb-3 text-xs font-semibold">
        <button type="button" @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 border border-slate-800'" class="px-4 py-2 rounded-xl cursor-pointer">Character Profile</button>
        <button type="button" @click="tab = 'services'" :class="tab === 'services' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 border border-slate-800'" class="px-4 py-2 rounded-xl cursor-pointer">Self-Services</button>
        <button type="button" @click="tab = 'privacy'" :class="tab === 'privacy' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 border border-slate-800'" class="px-4 py-2 rounded-xl cursor-pointer">Profile Customization &amp; Privacy</button>
        <button type="button" @click="tab = 'activity'" :class="tab === 'activity' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 border border-slate-800'" class="px-4 py-2 rounded-xl cursor-pointer">Service History</button>
    </div>

    <!-- Profile Tab -->
    <div x-show="tab === 'profile'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-2">Overview &amp; Appearance</h2>
            <div class="space-y-2 text-slate-300">
                <div class="flex justify-between"><span class="text-slate-400">Zeny</span><span class="font-bold text-amber-400">{{ number_format($character->zeny) }} Z</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Guild</span><span class="font-semibold">{{ $character->guild_name ?: 'None' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Location</span><span class="font-mono">{{ $character->last_map }} ({{ $character->last_x }}, {{ $character->last_y }})</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Hair Style / Color</span><span>#{{ $character->hair }} / #{{ $character->hair_color }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Clothes Color</span><span>#{{ $character->clothes_color }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Account Gender</span><span>{{ ($account->sex ?? 'M') === 'F' ? 'Female (F)' : 'Male (M)' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Last Login</span><span>{{ $character->last_login ?: 'Never' }}</span></div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-2">Attributes</h2>
            <div class="grid grid-cols-3 gap-3">
                @foreach(['str', 'agi', 'vit', 'int', 'dex', 'luk'] as $stat)
                    <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-slate-400 block">{{ $stat }}</span>
                        <span class="text-base font-bold text-white">{{ $character->{$stat} }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-800 text-slate-300">
                <span>Available Status Points: <strong class="text-cyan-400">{{ $character->status_point }}</strong></span>
                <span>Skill Points: <strong class="text-cyan-400">{{ $character->skill_point }}</strong></span>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3">
            <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-2">Currently Equipped</h2>
            @forelse($equipped as $eq)
                <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-2.5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-white block">{{ $eq->refine > 0 ? "+{$eq->refine} " : '' }}{{ $eq->item_name }}</span>
                        <span class="text-[10px] text-cyan-400">{{ $eq->equip_slot_name }} • #{{ $eq->nameid }}</span>
                    </div>
                </div>
            @empty
                <p class="text-slate-500">No items currently equipped.</p>
            @endforelse
        </div>
    </div>

    <!-- Self-Services Tab -->
    <div x-show="tab === 'services'" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        @if($settings['rename_enabled'])
            <form method="POST" action="{{ route('account.characters.services.rename', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-rename">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Character Rename</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['rename_cost']) }} CP</span>
                </div>
                <input type="text" name="new_name" required minlength="4" maxlength="23" placeholder="New character name (4-23 chars)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Rename Character</button>
            </form>
        @endif

        @if($settings['appearance_enabled'])
            @php
                $charSex = strtoupper((string) ($account->sex ?? $character->sex ?? 'M')) === 'F' ? 'F' : 'M';
                $maxHair = max(1, (int) $settings['appearance_max_hair']);
                $maxHairColor = max(0, (int) $settings['appearance_max_hair_color']);
                $maxClothesColor = max(0, (int) $settings['appearance_max_clothes_color']);
            @endphp
            <form method="POST" action="{{ route('account.characters.services.appearance', $character->char_id) }}"
                  class="md:col-span-2 bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-5"
                  x-data="{
                      gender: @js($charSex),
                      jobId: {{ (int) $character->class }},
                      jobName: @js($character->job_name),
                      hairStyle: {{ max(1, (int) $character->hair) }},
                      hairColor: {{ (int) $character->hair_color }},
                      clothesColor: {{ (int) $character->clothes_color }},
                      direction: 0,
                      hairColors: @js($hairColors),
                      proxyBaseUrl: @js(route('account.characters.sprite-preview')),
                      hairProxyBaseUrl: @js(route('account.characters.hair-preview')),
                      previewSpriteSrc: '',
                      previewFailed: false,
                      _debounceTimer: null,
                      init() {
                          this.refreshPreview();
                          this.$watch('hairStyle', () => this.schedulePreview());
                          this.$watch('hairColor', () => this.schedulePreview());
                          this.$watch('clothesColor', () => this.schedulePreview());
                          this.$watch('direction', () => this.schedulePreview());
                      },
                      schedulePreview() {
                          clearTimeout(this._debounceTimer);
                          this._debounceTimer = setTimeout(() => this.refreshPreview(), 80);
                      },
                      refreshPreview() {
                          this.previewFailed = false;
                          this.previewSpriteSrc = `${this.proxyBaseUrl}?gender=${this.gender}&job=${this.jobId}&hair=${this.hairStyle}&color=${this.hairColor}&dye=${this.clothesColor}&dir=${this.direction}`;
                      },
                      hairIconUrl(style) {
                          return `${this.hairProxyBaseUrl}?gender=${this.gender}&hair=${style}`;
                      },
                      get selectedHairColor() {
                          return this.hairColors[this.hairColor] || { name: 'Color #' + this.hairColor, hex: '#94a3b8' };
                      },
                      rotateLeft() { this.direction = (this.direction + 7) % 8; },
                      rotateRight() { this.direction = (this.direction + 1) % 8; }
                  }">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-appearance">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-white">Appearance Change</h3>
                        <p class="text-[11px] text-slate-400">Preview your character's actual job sprite, hairstyle, hair color, and clothes dye live.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cyan-500/15 text-cyan-300 font-bold self-start sm:self-auto">{{ number_format($settings['appearance_cost']) }} CP</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Live Character Sprite Stage -->
                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-5 space-y-4 h-fit">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Live Preview</span>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="rotateLeft()" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white cursor-pointer" title="Rotate Left">↺</button>
                                <button type="button" @click="rotateRight()" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white cursor-pointer" title="Rotate Right">↻</button>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-gradient-to-b from-slate-950 via-slate-900/90 to-slate-950 border border-slate-800 p-5 flex flex-col items-center justify-center text-center space-y-2">
                            <div class="relative w-28 h-32 flex items-center justify-center">
                                <img x-show="previewSpriteSrc && !previewFailed"
                                     :src="previewSpriteSrc"
                                     :alt="jobName"
                                     class="max-w-full max-h-full object-contain drop-shadow-[0_6px_12px_rgba(0,0,0,0.65)] scale-125"
                                     x-on:error="previewFailed = true">
                                <div x-show="!previewSpriteSrc || previewFailed" style="display: none;" class="flex flex-col items-center justify-center gap-1">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center border border-white/20 text-lg font-black text-white"
                                         :style="`background: radial-gradient(circle at 35% 30%, ${selectedHairColor.hex}, #0f172a 95%);`"
                                         x-text="gender === 'F' ? '♀' : '♂'"></div>
                                    <span class="text-[10px] font-bold text-cyan-300" x-text="jobName"></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-950 border border-slate-700/80 text-[11px] font-bold text-cyan-300" x-text="jobName"></span>
                            <p class="text-sm font-black text-white">{{ $character->name }}</p>
                        </div>

                        <div class="space-y-1.5 text-[11px] bg-slate-900/60 p-3.5 rounded-xl border border-slate-800/80">
                            <div class="flex justify-between"><span class="text-slate-400">Hair Style</span><span class="font-mono font-bold text-cyan-300" x-text="'#' + hairStyle"></span></div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Hair Color</span>
                                <span class="inline-flex items-center gap-1.5 font-semibold text-white">
                                    <span class="w-3 h-3 rounded-full border border-white/40" :style="`background-color: ${selectedHairColor.hex};`"></span>
                                    <span x-text="selectedHairColor.name"></span>
                                </span>
                            </div>
                            <div class="flex justify-between"><span class="text-slate-400">Clothes Dye</span><span class="font-mono font-bold text-amber-300" x-text="'Palette #' + clothesColor"></span></div>
                        </div>
                    </div>

                    <!-- Right: Hair Style Grid, Hair Color Swatches, Clothes Color -->
                    <div class="lg:col-span-2 space-y-5">
                        <!-- Hair Style Grid -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-slate-300">Hair Style ({{ $charSex === 'F' ? 'Female' : 'Male' }})</label>
                                <span class="text-[11px] font-mono text-cyan-400" x-text="'Selected: #' + hairStyle"></span>
                            </div>
                            <div class="grid grid-cols-5 sm:grid-cols-7 gap-2 max-h-52 overflow-y-auto pr-1">
                                @for($hStyle = 1; $hStyle <= $maxHair; $hStyle++)
                                    <label @click="hairStyle = {{ $hStyle }}"
                                           :class="Number(hairStyle) === {{ $hStyle }} ? 'bg-cyan-500/20 border-cyan-400 ring-2 ring-cyan-500/40 text-white' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700'"
                                           class="p-2 rounded-xl border flex flex-col items-center gap-1 cursor-pointer transition-all">
                                        <input type="radio" name="hair" value="{{ $hStyle }}" x-model="hairStyle" class="hidden">
                                        <div class="w-10 h-10 rounded-lg bg-slate-900/90 border border-slate-800 flex items-center justify-center overflow-hidden">
                                            <img :src="hairIconUrl({{ $hStyle }})"
                                                 loading="lazy"
                                                 alt="Style #{{ $hStyle }}"
                                                 class="w-9 h-9 object-contain"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <span style="display: none;" class="text-xs font-bold text-slate-300">{{ $charSex === 'F' ? '♀' : '♂' }}</span>
                                        </div>
                                        <span class="text-[10px] font-mono font-bold">#{{ $hStyle }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <!-- Hair Color Palette -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-slate-300">Hair Color</label>
                                <span class="text-[11px] text-slate-400" x-text="selectedHairColor.name"></span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @for($hColor = 0; $hColor <= $maxHairColor; $hColor++)
                                    @php $cInfo = $hairColors[$hColor] ?? ['name' => "Color #{$hColor}", 'hex' => '#94a3b8']; @endphp
                                    <label @click.prevent="hairColor = {{ $hColor }}"
                                           :class="Number(hairColor) === {{ $hColor }} ? 'border-cyan-400 bg-slate-800 ring-2 ring-cyan-500/40' : 'border-slate-800 bg-slate-950/60'"
                                           class="px-3 py-1.5 rounded-xl border flex items-center gap-2 text-xs text-slate-200 cursor-pointer transition-all"
                                           title="{{ $cInfo['name'] }}">
                                        <input type="radio" name="hair_color" value="{{ $hColor }}" :checked="Number(hairColor) === {{ $hColor }}" class="hidden">
                                        <span class="w-4 h-4 rounded-md border border-white/40 shrink-0" style="background-color: {{ $cInfo['hex'] }};"></span>
                                        <span>{{ $cInfo['name'] }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <!-- Clothes Color Palette -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-slate-300">Clothes Color (Palette)</label>
                                <span class="text-[11px] font-mono text-amber-300" x-text="'Palette #' + clothesColor"></span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @for($cDye = 0; $cDye <= $maxClothesColor; $cDye++)
                                    <label @click.prevent="clothesColor = {{ $cDye }}"
                                           :class="Number(clothesColor) === {{ $cDye }} ? 'border-amber-400 bg-amber-500/15 text-amber-200 ring-2 ring-amber-500/30' : 'border-slate-800 bg-slate-950/60 text-slate-300'"
                                           class="px-3 py-1.5 rounded-xl border text-xs font-mono font-bold cursor-pointer transition-all">
                                        <input type="radio" name="clothes_color" value="{{ $cDye }}" :checked="Number(clothesColor) === {{ $cDye }}" class="hidden">
                                        <span>#{{ $cDye }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold transition-colors cursor-pointer">
                                Apply Appearance Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        @if($settings['job_change_enabled'])
            @php
                $jobSex = strtoupper((string) ($account->sex ?? $character->sex ?? 'M')) === 'F' ? 'F' : 'M';
            @endphp
            <form method="POST" action="{{ route('account.characters.services.job', $character->char_id) }}"
                  class="md:col-span-2 bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4"
                  x-data="{
                      gender: @js($jobSex),
                      hairStyle: {{ max(1, (int) $character->hair) }},
                      hairColor: {{ (int) $character->hair_color }},
                      clothesColor: {{ (int) $character->clothes_color }},
                      selectedClass: {{ (int) $character->class }},
                      jobSearch: '',
                      jobs: @js($availableJobs),
                      proxyBaseUrl: @js(route('account.characters.sprite-preview')),
                      previewFailed: false,
                      get filteredJobs() {
                          const q = (this.jobSearch || '').trim().toLowerCase();
                          if (!q) return this.jobs;
                          return this.jobs.filter(j => j.name.toLowerCase().includes(q) || String(j.id).includes(q));
                      },
                      get selectedJobObj() {
                          return this.jobs.find(j => Number(j.id) === Number(this.selectedClass)) || { id: this.selectedClass, name: @js($character->job_name) };
                      },
                      get previewUrl() {
                          return `${this.proxyBaseUrl}?gender=${this.gender}&job=${this.selectedClass}&hair=${this.hairStyle}&color=${this.hairColor}&dye=${this.clothesColor}&dir=0`;
                      }
                  }"
                  x-effect="selectedClass; previewFailed = false;"
                  onsubmit="return confirm('Change character job class?{{ $settings['job_change_reset_skills'] ? ' Learned skills will be reset and skill points refunded.' : '' }}');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-job">
                <input type="hidden" name="target_class" :value="selectedClass">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-white">Job / Class Change</h3>
                        <p class="text-[11px] text-slate-400">
                            Preview and switch your character's job class.
                            @if($settings['job_change_reset_skills'])
                                Learned skills are automatically reset and skill points refunded.
                            @endif
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cyan-500/15 text-cyan-300 font-bold self-start sm:self-auto">{{ number_format($settings['job_change_cost']) }} CP</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-5 flex flex-col items-center text-center space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Job Preview</span>
                        <div class="w-28 h-32 flex items-center justify-center">
                            <img x-show="!previewFailed" :src="previewUrl" :alt="selectedJobObj.name"
                                 class="max-w-full max-h-full object-contain drop-shadow-[0_6px_12px_rgba(0,0,0,0.65)] scale-125"
                                 x-on:error="previewFailed = true">
                            <span x-show="previewFailed" style="display: none;" class="text-xs font-bold text-cyan-300" x-text="selectedJobObj.name"></span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-900 border border-slate-700 text-[11px] font-bold text-cyan-300" x-text="selectedJobObj.name"></span>
                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Class ID #' + selectedClass"></span>
                    </div>

                    <div class="lg:col-span-2 space-y-3">
                        <input type="text" x-model="jobSearch" placeholder="Search job name or ID (e.g. Rune Knight, 4054)..."
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-56 overflow-y-auto pr-1">
                            <template x-for="job in filteredJobs" :key="job.id">
                                <button type="button" @click="selectedClass = job.id"
                                        :class="Number(selectedClass) === Number(job.id) ? 'bg-cyan-500/20 border-cyan-400 text-white ring-1 ring-cyan-500/40' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:border-slate-700'"
                                        class="p-2.5 rounded-xl border text-left transition-all cursor-pointer flex items-center justify-between gap-2">
                                    <span class="font-semibold truncate" x-text="job.name"></span>
                                    <span class="text-[10px] font-mono text-slate-500 shrink-0" x-text="'#' + job.id"></span>
                                </button>
                            </template>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" :disabled="Number(selectedClass) === {{ (int) $character->class }}"
                                    class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 disabled:opacity-50 text-slate-950 font-bold transition-colors cursor-pointer">
                                Change Job Class
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        @if($settings['stat_reset_enabled'])
            <form method="POST" action="{{ route('account.characters.services.stat-reset', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Reset all character attributes?');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-stat">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Stat Reset</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['stat_reset_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Resets STR/AGI/VIT/INT/DEX/LUK to 1 and refunds all spent status points. Min Level: {{ $settings['stat_reset_min_level'] }}.</p>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold cursor-pointer">Reset Stats</button>
            </form>
        @endif

        @if($settings['skill_reset_enabled'])
            <form method="POST" action="{{ route('account.characters.services.skill-reset', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Reset all learned skills?');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-skill">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Skill Reset</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['skill_reset_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Resets learned skills and returns all spent skill points. Min Level: {{ $settings['skill_reset_min_level'] }}.</p>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold cursor-pointer">Reset Skills</button>
            </form>
        @endif

        @if($settings['gender_enabled'])
            <form method="POST" action="{{ route('account.characters.services.gender', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Change account gender? This affects all characters on your game account.');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-gender">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Account Gender Change</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['gender_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Note: rAthena stores gender at the account level. All characters on your account must be offline.</p>
                <div class="flex items-center gap-3">
                    <select name="gender" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <option value="M" {{ ($account->sex ?? 'M') === 'M' ? 'selected' : '' }}>Male (M)</option>
                        <option value="F" {{ ($account->sex ?? 'M') === 'F' ? 'selected' : '' }}>Female (F)</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Change Gender</button>
                </div>
            </form>
        @endif

        @if($settings['lock_enabled'] || $settings['deletion_enabled'])
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white">Security &amp; Deletion</h3>
                @if($settings['lock_enabled'] && !$active_lock)
                    <form method="POST" action="{{ route('account.characters.services.lock', $character->char_id) }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="action" value="lock">
                        <input type="text" name="reason" placeholder="Optional lock note..." class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold cursor-pointer">Self-Lock</button>
                    </form>
                @endif
                @if($settings['deletion_enabled'] && (!$deletion_request || $deletion_request->status !== 'pending'))
                    <form method="POST" action="{{ route('account.characters.services.deletion.request', $character->char_id) }}" onsubmit="return confirm('Schedule this character for deletion?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-950/60 hover:bg-rose-900 text-rose-300 border border-rose-500/30 font-bold cursor-pointer">
                            Request Deletion ({{ $settings['deletion_wait_hours'] }}h Wait)
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <!-- Privacy & Customization Tab -->
    <div x-show="tab === 'privacy'" style="display: none;" class="max-w-3xl text-xs">
        <form method="POST" action="{{ route('account.characters.privacy', $character->char_id) }}" enctype="multipart/form-data" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-5">
            @csrf
            @php
                $acctRankingsEnabled = app(\App\Services\Module\ModuleManager::class)->isEnabled('rankings');
                $privacyFields = [
                    'is_public' => 'Public Profile',
                    'show_char_info' => 'Basic Info',
                    'show_equipments' => 'Equipments',
                    'show_stats' => 'Stats & Attributes',
                    'show_zeny' => 'Zeny Balance',
                    'show_guild' => 'Guild Standing',
                    'show_last_login' => 'Last Login Time',
                ];
                if ($acctRankingsEnabled) {
                    $privacyFields['show_badges'] = 'Earned Badges';
                    $privacyFields['show_rankings'] = 'Leaderboard Standings';
                    $privacyFields['show_achievements'] = 'Achievements';
                }
            @endphp

            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-white">Profile Customization &amp; Privacy</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Control what appears on your public character profile.</p>
                </div>
                <span class="text-[10px] px-2.5 py-1 rounded-full font-bold uppercase {{ ($privacy->is_public ?? true) ? 'text-emerald-400 bg-emerald-950/60 border border-emerald-500/30' : 'text-amber-400 bg-amber-950/60 border border-amber-500/30' }}">
                    {{ ($privacy->is_public ?? true) ? 'Public' : 'Private' }}
                </span>
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2.5">Display &amp; Visibility</span>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-slate-300">
                    @foreach($privacyFields as $pField => $pLabel)
                        <label class="flex items-center gap-2 cursor-pointer hover:text-white bg-slate-950/60 border border-slate-800/80 rounded-xl px-3 py-2">
                            <input type="checkbox" name="{{ $pField }}" value="1" {{ ($privacy->$pField ?? true) ? 'checked' : '' }} class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-0">
                            <span>{{ $pLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Character Avatar / Portrait</span>
                <input type="url" name="avatar_url" value="{{ !empty($privacy->avatar_path) && filter_var($privacy->avatar_path, FILTER_VALIDATE_URL) ? $privacy->avatar_path : '' }}" placeholder="Image URL (e.g. from Google or RateMyServer)..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs placeholder:text-slate-600 focus:border-cyan-500">
                <div class="flex items-center gap-3">
                    <label class="text-xs text-slate-400 shrink-0">Or Upload:</label>
                    <input type="file" name="avatar_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700">
                </div>
                @if(!empty($privacy->avatar_path))
                    <label class="flex items-center gap-2 text-xs text-amber-400 cursor-pointer pt-1">
                        <input type="checkbox" name="remove_avatar" value="1" class="rounded bg-slate-900 border-slate-700 text-amber-500">
                        <span>Reset to default job portrait</span>
                    </label>
                @endif
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Adventurer Bio / Motto</span>
                <input type="text" name="bio" value="{{ $privacy->bio ?? '' }}" placeholder="Short adventurer motto or story..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs placeholder:text-slate-600 focus:border-cyan-500">
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold transition-colors cursor-pointer">
                Save Profile &amp; Privacy
            </button>
        </form>
    </div>

    <!-- Activity Tab -->
    <div x-show="tab === 'activity'" style="display: none;" class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden text-xs">
        <div class="px-5 py-3.5 bg-slate-950/60 border-b border-slate-800">
            <h3 class="text-sm font-bold text-white">Recent Service History</h3>
        </div>
        <div class="divide-y divide-slate-800/50">
            @forelse($logs as $log)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-cyan-300">{{ $log->action_type }}</span>
                        <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] {{ $log->result === 'success' ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400' }}">{{ $log->result }}</span>
                        @if($log->service_cost > 0)
                            <span class="text-slate-400 ml-2">-{{ number_format($log->service_cost) }} CP</span>
                        @endif
                    </div>
                    <span class="text-slate-500">{{ $log->created_at->format('M d, Y H:i') }}</span>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-slate-500">No service activity recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
