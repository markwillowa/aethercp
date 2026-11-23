<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AetherCP Setup Wizard &bull; Ragnarok Online Control Panel Installation</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-theme-primary: #06b6d4;
            --color-theme-accent: #3b82f6;
            --color-theme-bg: #070a13;
            --color-theme-surface: #0f172a;
        }
        html, body {
            background-color: #070a13 !important;
            color: #e2e8f0 !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans antialiased text-slate-200 selection:bg-cyan-500 selection:text-black">
    <!-- Ethereal Background Orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Wizard Container -->
    <div class="relative z-10 flex-grow flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8"
         x-data="{
             step: 1,
             submitting: false,
             statusMessage: '',
             statusError: '',
             allRequirementsPassed: {{ $requirements['all_pass'] ? 'true' : 'false' }},

             // Step 2 Data
             appName: 'AetherCP',
             serverName: 'FreynixRO',
             appUrl: '{{ $suggestedUrl }}',

             // Step 3 Data: Web DB
             dbHost: '127.0.0.1',
             dbPort: 3306,
             dbDatabase: 'aether_cp',
             dbUsername: 'root',
             dbPassword: '',
             webDbTested: false,
             webDbTesting: false,
             webDbMessage: '',

             // Step 3 Data: Ragnarok Game DB
             roDbHost: '127.0.0.1',
             roDbPort: 3306,
             roDbDatabase: 'freynix',
             roDbUsername: 'root',
             roDbPassword: '',
             roEmulator: 'rathena',
             roRenewal: true,
             roPasswordHash: 'plain',
             gameDbTested: false,
             gameDbTesting: false,
             gameDbMessage: '',
             gameDbWarning: false,

             // Step 4 Data: Migration
             migrating: false,
             migrationComplete: false,
             migrationLog: '',

             // Step 5 Data: Admin
             adminType: 'designate_game_account',
             adminUsername: 'admin',
             adminEmail: 'admin@freynix.ro',
             adminPassword: '',

             async testWebDb() {
                 this.webDbTesting = true;
                 this.webDbMessage = '';
                 this.statusError = '';
                 try {
                     const res = await fetch('{{ route('install.test-database') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             db_host: this.dbHost,
                             db_port: this.dbPort,
                             db_database: this.dbDatabase,
                             db_username: this.dbUsername,
                             db_password: this.dbPassword
                         })
                     });
                     const data = await res.json();
                     if (res.ok && data.success) {
                         this.webDbTested = true;
                         this.webDbMessage = data.message;
                     } else {
                         this.webDbTested = false;
                         this.statusError = data.message || 'Web database connection failed.';
                     }
                 } catch (err) {
                     this.webDbTested = false;
                     this.statusError = err.message;
                 } finally {
                     this.webDbTesting = false;
                 }
             },

             async testGameDb() {
                 this.gameDbTesting = true;
                 this.gameDbMessage = '';
                 this.statusError = '';
                 try {
                     const res = await fetch('{{ route('install.test-game-database') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             ro_db_host: this.roDbHost,
                             ro_db_port: this.roDbPort,
                             ro_db_database: this.roDbDatabase,
                             ro_db_username: this.roDbUsername,
                             ro_db_password: this.roDbPassword,
                             ro_emulator: this.roEmulator
                         })
                     });
                     const data = await res.json();
                     if (res.ok && data.success) {
                         this.gameDbTested = true;
                         this.gameDbWarning = data.warning;
                         this.gameDbMessage = data.message;
                     } else {
                         this.gameDbTested = false;
                         this.statusError = data.message || 'Game database connection failed.';
                     }
                 } catch (err) {
                     this.gameDbTested = false;
                     this.statusError = err.message;
                 } finally {
                     this.gameDbTesting = false;
                 }
             },

             async runSetup() {
                 this.migrating = true;
                 this.statusError = '';
                 this.statusMessage = 'Writing configuration and provisioning database tables...';
                 try {
                     const res = await fetch('{{ route('install.setup-environment') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             app_name: this.appName,
                             server_name: this.serverName,
                             app_url: this.appUrl,
                             db_host: this.dbHost,
                             db_port: this.dbPort,
                             db_database: this.dbDatabase,
                             db_username: this.dbUsername,
                             db_password: this.dbPassword,
                             ro_db_host: this.roDbHost,
                             ro_db_port: this.roDbPort,
                             ro_db_database: this.roDbDatabase,
                             ro_db_username: this.roDbUsername,
                             ro_db_password: this.roDbPassword,
                             ro_emulator: this.roEmulator,
                             ro_renewal: this.roRenewal,
                             ro_password_hash: this.roPasswordHash
                         })
                     });
                     const data = await res.json();
                     if (res.ok && data.success) {
                         this.migrationComplete = true;
                         this.migrationLog = data.migration_log || '';
                         this.statusMessage = data.message;
                         this.step = 5;
                     } else {
                         this.statusError = data.message || 'Database provisioning failed.';
                     }
                 } catch (err) {
                     this.statusError = err.message;
                 } finally {
                     this.migrating = false;
                 }
             },

             async completeAdmin() {
                 this.submitting = true;
                 this.statusError = '';
                 try {
                     const res = await fetch('{{ route('install.setup-admin') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             admin_type: this.adminType,
                             username: this.adminUsername,
                             email: this.adminEmail,
                             password: this.adminPassword
                         })
                     });
                     const data = await res.json();
                     if (res.ok && data.success) {
                         this.step = 6;
                     } else {
                         this.statusError = data.message || 'Admin configuration failed.';
                     }
                 } catch (err) {
                     this.statusError = err.message;
                 } finally {
                     this.submitting = false;
                 }
             }
         }">

        <!-- Header / Logo -->
        <div class="sm:mx-auto sm:w-full sm:max-w-2xl text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 mb-4 shadow-xl shadow-cyan-500/20">
                <i data-lucide="layers" class="w-8 h-8 text-slate-950 font-bold"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">
                AetherCP <span class="aether-gradient-text">Installation</span>
            </h1>
            <p class="mt-2 text-sm text-slate-400">
                Independent, high-performance Ragnarok Online web control panel setup wizard.
            </p>

            <!-- Wizard Step Breadcrumbs -->
            <div class="mt-8 flex items-center justify-center space-x-2 text-xs">
                <span :class="step >= 1 ? 'bg-cyan-500 text-slate-950 font-bold shadow-md shadow-cyan-500/30' : 'bg-slate-800 text-slate-400'" class="w-7 h-7 rounded-full flex items-center justify-center transition-all">1</span>
                <span class="w-4 h-0.5" :class="step >= 2 ? 'bg-cyan-500' : 'bg-slate-800'"></span>
                <span :class="step >= 2 ? 'bg-cyan-500 text-slate-950 font-bold shadow-md shadow-cyan-500/30' : 'bg-slate-800 text-slate-400'" class="w-7 h-7 rounded-full flex items-center justify-center transition-all">2</span>
                <span class="w-4 h-0.5" :class="step >= 3 ? 'bg-cyan-500' : 'bg-slate-800'"></span>
                <span :class="step >= 3 ? 'bg-cyan-500 text-slate-950 font-bold shadow-md shadow-cyan-500/30' : 'bg-slate-800 text-slate-400'" class="w-7 h-7 rounded-full flex items-center justify-center transition-all">3</span>
                <span class="w-4 h-0.5" :class="step >= 4 ? 'bg-cyan-500' : 'bg-slate-800'"></span>
                <span :class="step >= 4 ? 'bg-cyan-500 text-slate-950 font-bold shadow-md shadow-cyan-500/30' : 'bg-slate-800 text-slate-400'" class="w-7 h-7 rounded-full flex items-center justify-center transition-all">4</span>
                <span class="w-4 h-0.5" :class="step >= 5 ? 'bg-cyan-500' : 'bg-slate-800'"></span>
                <span :class="step >= 5 ? 'bg-cyan-500 text-slate-950 font-bold shadow-md shadow-cyan-500/30' : 'bg-slate-800 text-slate-400'" class="w-7 h-7 rounded-full flex items-center justify-center transition-all">5</span>
                <span class="w-4 h-0.5" :class="step >= 6 ? 'bg-cyan-500' : 'bg-slate-800'"></span>
                <span :class="step >= 6 ? 'bg-emerald-400 text-slate-950 font-bold shadow-md shadow-emerald-400/30' : 'bg-slate-800 text-slate-400'" class="w-7 h-7 rounded-full flex items-center justify-center transition-all"><i data-lucide="check" class="w-4 h-4"></i></span>
            </div>
        </div>

        <!-- Wizard Card -->
        <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
            <div class="bg-slate-900/90 border border-slate-800/80 backdrop-blur-xl py-8 px-6 sm:px-10 rounded-3xl shadow-2xl relative">

                <!-- Global Error Alert -->
                <div x-show="statusError" x-cloak class="mb-6 p-4 rounded-xl bg-red-950/70 border border-red-500/40 text-red-200 text-xs flex items-start gap-3">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-400 shrink-0 mt-0.5"></i>
                    <div class="flex-grow">
                        <strong class="font-bold">Error:</strong>
                        <span x-text="statusError"></span>
                    </div>
                </div>

                <!-- Global Success Alert -->
                <div x-show="statusMessage" x-cloak class="mb-6 p-4 rounded-xl bg-emerald-950/70 border border-emerald-500/40 text-emerald-200 text-xs flex items-start gap-3">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5"></i>
                    <div class="flex-grow">
                        <span x-text="statusMessage"></span>
                    </div>
                </div>

                <!-- =================================================================== -->
                <!-- STEP 1: Pre-flight System & Environment Requirements -->
                <!-- =================================================================== -->
                <div x-show="step === 1" class="space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i data-lucide="cpu" class="w-5 h-5 text-cyan-400"></i>
                            <span>Step 1: System Pre-flight Checks</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Verifying PHP runtime, required extensions, and file system permissions.
                        </p>
                    </div>

                    <!-- PHP Version -->
                    <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-slate-200">PHP Version (>= 8.2)</span>
                            <p class="text-[11px] text-slate-400">Current version: {{ $requirements['php']['version'] }}</p>
                        </div>
                        <div>
                            @if($requirements['php']['pass'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Pass
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-950/80 text-red-400 border border-red-500/30">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i> Fail
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Extensions Grid -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Required PHP Extensions</h3>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            @foreach($requirements['extensions'] as $ext => $item)
                                <div class="bg-slate-950/50 border border-slate-800/80 rounded-xl p-2.5 flex items-center justify-between">
                                    <span class="text-slate-300">{{ $item['name'] }}</span>
                                    @if($item['loaded'])
                                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                                    @else
                                        <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- File Permissions -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Directory Write Access</h3>
                        <div class="space-y-2 text-xs">
                            @foreach($requirements['paths'] as $key => $pathInfo)
                                <div class="bg-slate-950/50 border border-slate-800/80 rounded-xl p-2.5 flex items-center justify-between">
                                    <span class="font-mono text-[11px] text-slate-300">{{ $pathInfo['path'] }}</span>
                                    @if($pathInfo['writable'])
                                        <span class="text-emerald-400 font-semibold flex items-center gap-1"><i data-lucide="check" class="w-3.5 h-3.5"></i> Writable</span>
                                    @else
                                        <span class="text-red-400 font-semibold flex items-center gap-1"><i data-lucide="x" class="w-3.5 h-3.5"></i> Unwritable</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="button" @click="step = 2" :disabled="!allRequirementsPassed"
                                class="px-6 py-2.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-all">
                            <span>Proceed to Realm Settings</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- =================================================================== -->
                <!-- STEP 2: General & Realm Settings -->
                <!-- =================================================================== -->
                <div x-show="step === 2" class="space-y-6" x-cloak>
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i data-lucide="globe" class="w-5 h-5 text-cyan-400"></i>
                            <span>Step 2: General Realm Settings</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Configure your server identity and public web address.
                        </p>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Control Panel Name</label>
                            <input type="text" x-model="appName" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-slate-100 placeholder-slate-500">
                            <p class="text-[11px] text-slate-500 mt-1">Default branding displayed in the navigation and metadata.</p>
                        </div>

                        <div>
                            <label class="block font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Ragnarok Server Name</label>
                            <input type="text" x-model="serverName" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-slate-100 placeholder-slate-500">
                            <p class="text-[11px] text-slate-500 mt-1">The name of your Ragnarok Online server (e.g. FreynixRO).</p>
                        </div>

                        <div>
                            <label class="block font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Application URL</label>
                            <input type="text" x-model="appUrl" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-slate-100 font-mono">
                            <p class="text-[11px] text-slate-500 mt-1">The public root URL of your control panel without trailing slash or /public (e.g. http://localhost/aether or https://myro.com).</p>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 1" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800/80">
                            Back
                        </button>
                        <button type="button" @click="step = 3" class="px-6 py-2.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/20 flex items-center gap-2 transition-all">
                            <span>Proceed to Database Setup</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- =================================================================== -->
                <!-- STEP 3: Database Configuration (Web DB & RO Game DB) -->
                <!-- =================================================================== -->
                <div x-show="step === 3" class="space-y-6" x-cloak>
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i data-lucide="database" class="w-5 h-5 text-cyan-400"></i>
                            <span>Step 3: Dual Database Configuration</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            AetherCP cleanly isolates its own web database from your Ragnarok game server database.
                        </p>
                    </div>

                    <!-- Web Database Section -->
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">1. AetherCP Web Database</h3>
                            </div>
                            <button type="button" @click="testWebDb()" :disabled="webDbTesting"
                                    class="px-3 py-1 rounded-lg text-xs font-semibold bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 transition-all flex items-center gap-1.5">
                                <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                                <span x-text="webDbTesting ? 'Testing...' : 'Test Connection'"></span>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block text-slate-400 mb-1">Host</label>
                                <input type="text" x-model="dbHost" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Port</label>
                                <input type="number" x-model="dbPort" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Database Name</label>
                                <input type="text" x-model="dbDatabase" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Username</label>
                                <input type="text" x-model="dbUsername" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Password</label>
                                <input type="password" x-model="dbPassword" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                        </div>

                        <div x-show="webDbMessage" class="text-xs text-emerald-400 flex items-center gap-1.5 font-medium">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span x-text="webDbMessage"></span>
                        </div>
                    </div>

                    <!-- Ragnarok Game Database Section -->
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">2. Ragnarok Game Database</h3>
                            </div>
                            <button type="button" @click="testGameDb()" :disabled="gameDbTesting"
                                    class="px-3 py-1 rounded-lg text-xs font-semibold bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 transition-all flex items-center gap-1.5">
                                <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                                <span x-text="gameDbTesting ? 'Testing...' : 'Test Game DB'"></span>
                            </button>
                        </div>

                        <!-- Important emulator data reminder banner -->
                        <div class="p-3 bg-amber-950/40 border border-amber-500/30 rounded-xl text-[11px] text-amber-200 leading-relaxed flex items-start gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i>
                            <div>
                                <strong>Official SQL Tables Requirement:</strong> In accordance with standard rAthena & Hercules practices, your game tables (<code>login</code>, <code>char</code>, <code>guild</code>, <code>item_db</code>) must be imported directly from the emulator's official SQL files. AetherCP does not alter or run migrations against your core game server tables.
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block text-slate-400 mb-1">Emulator Core</label>
                                <select x-model="roEmulator" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    <option value="rathena">rAthena</option>
                                    <option value="hercules">Hercules</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game Mode</label>
                                <select x-model="roRenewal" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    <option :value="true">Renewal (4th Jobs / Episode 19+)</option>
                                    <option :value="false">Pre-Renewal (Classic / Transcendent)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB Host</label>
                                <input type="text" x-model="roDbHost" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB Port</label>
                                <input type="number" x-model="roDbPort" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB Name</label>
                                <input type="text" x-model="roDbDatabase" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB User</label>
                                <input type="text" x-model="roDbUsername" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB Password</label>
                                <input type="password" x-model="roDbPassword" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Password Encryption</label>
                                <select x-model="roPasswordHash" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    <option value="plain">Plaintext</option>
                                    <option value="md5">MD5</option>
                                    <option value="sha256">SHA256</option>
                                </select>
                            </div>
                        </div>

                        <div x-show="gameDbMessage" :class="gameDbWarning ? 'text-amber-400' : 'text-emerald-400'" class="text-xs flex items-center gap-1.5 font-medium">
                            <i :data-lucide="gameDbWarning ? 'alert-circle' : 'check-circle'" class="w-4 h-4"></i>
                            <span x-text="gameDbMessage"></span>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 2" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800/80">
                            Back
                        </button>
                        <button type="button" @click="step = 4" class="px-6 py-2.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/20 flex items-center gap-2 transition-all">
                            <span>Proceed to Migration</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- =================================================================== -->
                <!-- STEP 4: Migration & Seed Execution -->
                <!-- =================================================================== -->
                <div x-show="step === 4" class="space-y-6" x-cloak>
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i data-lucide="hammer" class="w-5 h-5 text-cyan-400"></i>
                            <span>Step 4: Provision Web Database</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Save your configuration, generate application encryption key, and create AetherCP tables.
                        </p>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-6 text-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center mx-auto text-cyan-400">
                            <i data-lucide="database-zap" class="w-8 h-8" :class="migrating ? 'animate-pulse' : ''"></i>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-white">Ready to Execute Migrations</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                                This will provision the news articles, site settings, administrative activity logs, user sessions, and donation tables in <strong class="text-cyan-400" x-text="dbDatabase"></strong>.
                            </p>
                        </div>

                        <div class="pt-2">
                            <button type="button" @click="runSetup()" :disabled="migrating"
                                    class="px-8 py-3 rounded-xl font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-xl shadow-cyan-500/30 disabled:opacity-50 flex items-center justify-center gap-2 mx-auto transition-all">
                                <i data-lucide="play" class="w-4 h-4" x-show="!migrating"></i>
                                <span x-text="migrating ? 'Provisioning Tables...' : 'Execute Database Provisioning'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 3" :disabled="migrating" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800/80">
                            Back
                        </button>
                    </div>
                </div>

                <!-- =================================================================== -->
                <!-- STEP 5: Administrator Setup -->
                <!-- =================================================================== -->
                <div x-show="step === 5" class="space-y-6" x-cloak>
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-5 h-5 text-cyan-400"></i>
                            <span>Step 5: Administrative Privileges</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Choose how you wish to access the AetherCP Admin Control Panel.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label class="block bg-slate-950/70 border border-slate-800 rounded-xl p-4 cursor-pointer hover:border-cyan-500/40 transition-colors"
                               :class="adminType === 'designate_game_account' ? 'border-cyan-500/60 bg-cyan-950/10' : ''">
                            <div class="flex items-start gap-3">
                                <input type="radio" x-model="adminType" value="designate_game_account" class="mt-1 text-cyan-500 focus:ring-cyan-500">
                                <div>
                                    <span class="text-xs font-bold text-white">Elevate Existing Game Account (Recommended)</span>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Grants admin role (group_id = 99) to a character/game account already registered in your rAthena/Hercules database.</p>
                                </div>
                            </div>
                        </label>

                        <label class="block bg-slate-950/70 border border-slate-800 rounded-xl p-4 cursor-pointer hover:border-cyan-500/40 transition-colors"
                               :class="adminType === 'create_new_account' ? 'border-cyan-500/60 bg-cyan-950/10' : ''">
                            <div class="flex items-start gap-3">
                                <input type="radio" x-model="adminType" value="create_new_account" class="mt-1 text-cyan-500 focus:ring-cyan-500">
                                <div>
                                    <span class="text-xs font-bold text-white">Create Standalone Web Administrator</span>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Creates a dedicated web-only administrative user in AetherCP.</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-5 space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1" x-text="adminType === 'designate_game_account' ? 'Game Account Username' : 'Administrator Username'"></label>
                            <input type="text" x-model="adminUsername" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Notification Email</label>
                            <input type="email" x-model="adminEmail" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                        </div>

                        <div x-show="adminType === 'create_new_account'">
                            <label class="block font-semibold text-slate-300 mb-1">Admin Password</label>
                            <input type="password" x-model="adminPassword" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="button" @click="completeAdmin()" :disabled="submitting"
                                class="px-8 py-3 rounded-xl font-bold bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 shadow-xl shadow-emerald-500/20 disabled:opacity-50 flex items-center gap-2 transition-all">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span x-text="submitting ? 'Finalizing Setup...' : 'Complete Installation'"></span>
                        </button>
                    </div>
                </div>

                <!-- =================================================================== -->
                <!-- STEP 6: Complete & Congratulations -->
                <!-- =================================================================== -->
                <div x-show="step === 6" class="space-y-6 text-center py-4" x-cloak>
                    <div class="w-20 h-20 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center mx-auto text-emerald-400 mb-4 shadow-xl shadow-emerald-500/20">
                        <i data-lucide="sparkles" class="w-10 h-10"></i>
                    </div>

                    <div>
                        <h2 class="text-2xl font-black text-white">Installation Complete!</h2>
                        <p class="text-xs text-slate-400 mt-2 max-w-md mx-auto leading-relaxed">
                            AetherCP has been successfully installed, locked, and configured for <strong class="text-cyan-400" x-text="serverName"></strong>.
                        </p>
                    </div>

                    <div class="p-4 bg-slate-950 border border-slate-800 rounded-2xl text-left text-xs space-y-2 max-w-md mx-auto text-slate-300">
                        <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="text-slate-500">Public Root URL:</span>
                            <span class="font-mono text-cyan-400" x-text="appUrl"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="text-slate-500">Web Database:</span>
                            <span class="font-mono text-slate-200" x-text="dbDatabase"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="text-slate-500">Game Database:</span>
                            <span class="font-mono text-slate-200" x-text="roDbDatabase"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Administrator:</span>
                            <strong class="text-amber-400" x-text="adminUsername"></strong>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/20 flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="home" class="w-4 h-4"></i>
                            <span>Visit Public Site</span>
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span>Admin Control Panel</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
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
