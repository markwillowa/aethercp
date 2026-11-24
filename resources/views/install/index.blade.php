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
             appName: '{{ addslashes($defaultConfig['app_name']) }}',
             serverName: '{{ addslashes($defaultConfig['server_name']) }}',
             appUrl: '{{ addslashes($defaultConfig['app_url']) }}',

             // Step 3 Data: Web DB
             dbHost: '{{ addslashes($defaultConfig['db_host']) }}',
             dbPort: {{ (int)$defaultConfig['db_port'] }},
             dbDatabase: '{{ addslashes($defaultConfig['db_database']) }}',
             dbUsername: '{{ addslashes($defaultConfig['db_username']) }}',
             dbPassword: '{{ addslashes($defaultConfig['db_password']) }}',
             webDbTested: false,
             webDbTesting: false,
             webDbMessage: '',

             // Step 3 Data: Ragnarok Game DB
             roDbHost: '{{ addslashes($defaultConfig['ro_db_host']) }}',
             roDbPort: {{ (int)$defaultConfig['ro_db_port'] }},
             roDbDatabase: '{{ addslashes($defaultConfig['ro_db_database']) }}',
             roDbUsername: '{{ addslashes($defaultConfig['ro_db_username']) }}',
             roDbPassword: '{{ addslashes($defaultConfig['ro_db_password']) }}',
             roEmulator: '{{ addslashes($defaultConfig['ro_emulator']) }}',
             roRenewal: {{ $defaultConfig['ro_renewal'] ? 'true' : 'false' }},
             roPasswordHash: '{{ addslashes($defaultConfig['ro_password_hash']) }}',
             roLoginIp: '{{ addslashes($defaultConfig['ro_login_ip'] ?? '127.0.0.1') }}',
             roLoginPort: {{ (int)($defaultConfig['ro_login_port'] ?? 6900) }},
             roCharIp: '{{ addslashes($defaultConfig['ro_char_ip'] ?? '127.0.0.1') }}',
             roCharPort: {{ (int)($defaultConfig['ro_char_port'] ?? 6121) }},
             roMapIp: '{{ addslashes($defaultConfig['ro_map_ip'] ?? '127.0.0.1') }}',
             roMapPort: {{ (int)($defaultConfig['ro_map_port'] ?? 5121) }},
             roRateBase: '{{ addslashes($defaultConfig['ro_rate_base'] ?? '1000x') }}',
             roRateJob: '{{ addslashes($defaultConfig['ro_rate_job'] ?? '1000x') }}',
             roRateDrop: '{{ addslashes($defaultConfig['ro_rate_drop'] ?? '100x') }}',
             roMaxLevel: {{ (int)($defaultConfig['ro_max_level'] ?? 260) }},
             showAdvRoConfig: false,
             gameDbTested: false,
             gameDbTesting: false,
             gameDbMessage: '',
             gameDbWarning: false,

             // Step 4 Data: Migration / Provisioning
             migrationMode: 'auto',
             migrating: false,
             verifyingManual: false,
             migrationComplete: false,
             migrationLog: '',

             // Step 5 Data: Admin
             adminType: 'designate_game_account',
             adminUsername: 'admin',
             adminEmail: 'admin@freynix.ro',
             adminPassword: '',

             // STAGE 1 VALIDATION
             proceedToStep2() {
                 this.statusError = '';
                 if (!this.allRequirementsPassed) {
                     this.statusError = 'Cannot proceed: One or more critical system requirements failed. Please ensure PHP 8.2+ and required extensions are installed.';
                     return;
                 }
                 this.step = 2;
             },

             // STAGE 2 VALIDATION
             proceedToStep3() {
                 this.statusError = '';
                 if (!this.appName || this.appName.trim().length < 2) {
                     this.statusError = 'Stage 2 Incomplete: Control Panel Name is required (minimum 2 characters).';
                     return;
                 }
                 if (!this.serverName || this.serverName.trim().length < 2) {
                     this.statusError = 'Stage 2 Incomplete: Ragnarok Server Name is required (minimum 2 characters).';
                     return;
                 }
                 const urlTrimmed = (this.appUrl || '').trim();
                 if (!urlTrimmed || !urlTrimmed.match(/^https?:\/\/.+/i)) {
                     this.statusError = 'Stage 2 Incomplete: A valid Application URL starting with http:// or https:// is required.';
                     return;
                 }
                 this.step = 3;
             },

             // STAGE 3 VALIDATION (DATABASE)
             async testWebDb() {
                 this.statusError = '';
                 this.webDbMessage = '';
                 if (!this.dbHost || !this.dbHost.trim()) {
                     this.statusError = 'Missing Web DB Host.';
                     return;
                 }
                 if (!this.dbPort || this.dbPort < 1 || this.dbPort > 65535) {
                     this.statusError = 'Invalid Web DB Port (1 - 65535).';
                     return;
                 }
                 if (!this.dbDatabase || !this.dbDatabase.trim()) {
                     this.statusError = 'Missing Web DB Database Name.';
                     return;
                 }
                 if (!this.dbUsername || !this.dbUsername.trim()) {
                     this.statusError = 'Missing Web DB Username.';
                     return;
                 }

                 this.webDbTesting = true;
                 try {
                     const res = await fetch('{{ route('install.test-database') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'Accept': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             db_host: this.dbHost.trim(),
                             db_port: this.dbPort,
                             db_database: this.dbDatabase.trim(),
                             db_username: this.dbUsername.trim(),
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
                     this.statusError = 'Web DB Connection Error: ' + err.message;
                 } finally {
                     this.webDbTesting = false;
                 }
             },

             async testGameDb() {
                 this.statusError = '';
                 this.gameDbMessage = '';
                 if (!this.roDbHost || !this.roDbHost.trim()) {
                     this.statusError = 'Missing Game DB Host.';
                     return;
                 }
                 if (!this.roDbPort || this.roDbPort < 1 || this.roDbPort > 65535) {
                     this.statusError = 'Invalid Game DB Port (1 - 65535).';
                     return;
                 }
                 if (!this.roDbDatabase || !this.roDbDatabase.trim()) {
                     this.statusError = 'Missing Game DB Database Name.';
                     return;
                 }
                 if (!this.roDbUsername || !this.roDbUsername.trim()) {
                     this.statusError = 'Missing Game DB Username.';
                     return;
                 }

                 this.gameDbTesting = true;
                 try {
                     const res = await fetch('{{ route('install.test-game-database') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'Accept': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             ro_db_host: this.roDbHost.trim(),
                             ro_db_port: this.roDbPort,
                             ro_db_database: this.roDbDatabase.trim(),
                             ro_db_username: this.roDbUsername.trim(),
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
                     this.statusError = 'Game DB Connection Error: ' + err.message;
                 } finally {
                     this.gameDbTesting = false;
                 }
             },

             proceedToStep4() {
                 this.statusError = '';
                 // 1. Check Web DB completeness
                 if (!this.dbHost || !this.dbHost.trim() || !this.dbDatabase || !this.dbDatabase.trim() || !this.dbUsername || !this.dbUsername.trim()) {
                     this.statusError = 'Stage 3 Incomplete: Please fill in all required fields for the AetherCP Web Database.';
                     return;
                 }
                 // 2. Check Game DB completeness
                 if (!this.roDbHost || !this.roDbHost.trim() || !this.roDbDatabase || !this.roDbDatabase.trim() || !this.roDbUsername || !this.roDbUsername.trim()) {
                     this.statusError = 'Stage 3 Incomplete: Please fill in all required fields for the Ragnarok Game Database.';
                     return;
                 }
                 // 3. Strict verification requirement: Both tests must pass
                 if (!this.webDbTested) {
                     this.statusError = 'Cannot proceed: You must click \'Test Connection\' on the AetherCP Web Database and verify connectivity first.';
                     return;
                 }
                 if (!this.gameDbTested) {
                     this.statusError = 'Cannot proceed: You must click \'Test Game DB\' on the Ragnarok Game Database and verify connectivity first.';
                     return;
                 }
                 this.step = 4;
             },

             // STAGE 4: MIGRATION & PROVISIONING
             async runSetup() {
                 this.migrating = true;
                 this.statusError = '';
                 this.statusMessage = 'Writing configuration and provisioning database tables...';
                 try {
                     const res = await fetch('{{ route('install.setup-environment') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'Accept': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             app_name: this.appName.trim(),
                             server_name: this.serverName.trim(),
                             app_url: this.appUrl.trim(),
                             db_host: this.dbHost.trim(),
                             db_port: this.dbPort,
                             db_database: this.dbDatabase.trim(),
                             db_username: this.dbUsername.trim(),
                             db_password: this.dbPassword,
                             ro_db_host: this.roDbHost.trim(),
                             ro_db_port: this.roDbPort,
                             ro_db_database: this.roDbDatabase.trim(),
                             ro_db_username: this.roDbUsername.trim(),
                             ro_db_password: this.roDbPassword,
                             ro_emulator: this.roEmulator,
                             ro_renewal: this.roRenewal,
                             ro_password_hash: this.roPasswordHash,
                             ro_login_ip: (this.roLoginIp || '').trim(),
                             ro_login_port: this.roLoginPort,
                             ro_char_ip: (this.roCharIp || '').trim(),
                             ro_char_port: this.roCharPort,
                             ro_map_ip: (this.roMapIp || '').trim(),
                             ro_map_port: this.roMapPort,
                             ro_rate_base: (this.roRateBase || '').trim(),
                             ro_rate_job: (this.roRateJob || '').trim(),
                             ro_rate_drop: (this.roRateDrop || '').trim(),
                             ro_max_level: this.roMaxLevel
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
                     this.statusError = 'Setup execution error: ' + err.message;
                 } finally {
                     this.migrating = false;
                 }
             },

             // STAGE 4: MANUAL SQL VERIFICATION (FOR CPANEL / PHPMYADMIN)
             async verifyManualSetup() {
                 this.verifyingManual = true;
                 this.statusError = '';
                 this.statusMessage = 'Verifying manually imported SQL schema and settings...';
                 try {
                     const res = await fetch('{{ route('install.verify-manual-sql') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'Accept': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             app_name: this.appName.trim(),
                             server_name: this.serverName.trim(),
                             app_url: this.appUrl.trim(),
                             db_host: this.dbHost.trim(),
                             db_port: this.dbPort,
                             db_database: this.dbDatabase.trim(),
                             db_username: this.dbUsername.trim(),
                             db_password: this.dbPassword,
                             ro_db_host: this.roDbHost.trim(),
                             ro_db_port: this.roDbPort,
                             ro_db_database: this.roDbDatabase.trim(),
                             ro_db_username: this.roDbUsername.trim(),
                             ro_db_password: this.roDbPassword,
                             ro_emulator: this.roEmulator,
                             ro_renewal: this.roRenewal,
                             ro_password_hash: this.roPasswordHash,
                             ro_login_ip: (this.roLoginIp || '').trim(),
                             ro_login_port: this.roLoginPort,
                             ro_char_ip: (this.roCharIp || '').trim(),
                             ro_char_port: this.roCharPort,
                             ro_map_ip: (this.roMapIp || '').trim(),
                             ro_map_port: this.roMapPort,
                             ro_rate_base: (this.roRateBase || '').trim(),
                             ro_rate_job: (this.roRateJob || '').trim(),
                             ro_rate_drop: (this.roRateDrop || '').trim(),
                             ro_max_level: this.roMaxLevel
                         })
                     });
                     const data = await res.json();
                     if (res.ok && data.success) {
                         this.migrationComplete = true;
                         this.statusMessage = data.message;
                         this.step = 5;
                     } else {
                         this.statusError = data.message || 'SQL verification failed. Please ensure both SQL files are imported.';
                     }
                 } catch (err) {
                     this.statusError = 'Manual verification error: ' + err.message;
                 } finally {
                     this.verifyingManual = false;
                 }
             },

             // STAGE 5 VALIDATION (ADMIN SETUP)
             async completeAdmin() {
                 this.statusError = '';
                 if (!this.adminUsername || this.adminUsername.trim().length < 2) {
                     this.statusError = 'Stage 5 Incomplete: Administrator username is required (minimum 2 characters).';
                     return;
                 }
                 if (!this.adminEmail || !this.adminEmail.trim().includes('@')) {
                     this.statusError = 'Stage 5 Incomplete: A valid notification email address is required.';
                     return;
                 }
                 if (this.adminType === 'create_new_account') {
                     if (!this.adminPassword || this.adminPassword.length < 6) {
                         this.statusError = 'Stage 5 Incomplete: Password is required when creating a standalone administrator (minimum 6 characters).';
                         return;
                     }
                 }

                 this.submitting = true;
                 try {
                     const res = await fetch('{{ route('install.setup-admin') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'Accept': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                         },
                         body: JSON.stringify({
                             admin_type: this.adminType,
                             username: this.adminUsername.trim(),
                             email: this.adminEmail.trim(),
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
                     this.statusError = 'Admin setup error: ' + err.message;
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
                <div x-show="statusError" x-cloak class="mb-6 p-4 rounded-xl bg-red-950/70 border border-red-500/40 text-red-200 text-xs flex items-start gap-3 animate-pulse">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-400 shrink-0 mt-0.5"></i>
                    <div class="flex-grow">
                        <strong class="font-bold">Cannot Proceed:</strong>
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
                            Verifying PHP runtime, required extensions, and file system permissions. All requirements must pass to continue.
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
                        <button type="button" @click="proceedToStep2()" :disabled="!allRequirementsPassed"
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
                            Configure your server identity and public web address. All fields are required.
                        </p>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Control Panel Name <span class="text-red-400">*</span></label>
                            <input type="text" x-model="appName" @input="statusError = ''" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-slate-100 placeholder-slate-500" placeholder="e.g. AetherCP">
                            <p class="text-[11px] text-slate-500 mt-1">Branding displayed in title, navbar, and metadata.</p>
                        </div>

                        <div>
                            <label class="block font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Ragnarok Server Name <span class="text-red-400">*</span></label>
                            <input type="text" x-model="serverName" @input="statusError = ''" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-slate-100 placeholder-slate-500" placeholder="e.g. FreynixRO">
                            <p class="text-[11px] text-slate-500 mt-1">The name of your Ragnarok Online server.</p>
                        </div>

                        <div>
                            <label class="block font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Application URL <span class="text-red-400">*</span></label>
                            <input type="text" x-model="appUrl" @input="statusError = ''" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-slate-100 font-mono" placeholder="http://localhost/aether">
                            <p class="text-[11px] text-slate-500 mt-1">The root URL without /public or trailing slash (must start with http:// or https://).</p>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 1; statusError = ''" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800/80">
                            Back
                        </button>
                        <button type="button" @click="proceedToStep3()" class="px-6 py-2.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/20 flex items-center gap-2 transition-all">
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
                            Both database connections must be tested and verified before you can proceed to the provisioning stage.
                        </p>
                    </div>

                    <!-- Web Database Section -->
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" :class="webDbTested ? 'bg-emerald-400' : 'bg-cyan-400'"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">1. AetherCP Web Database</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span x-show="webDbTested" class="text-xs text-emerald-400 font-bold flex items-center gap-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Verified
                                </span>
                                <button type="button" @click="testWebDb()" :disabled="webDbTesting"
                                        class="px-3 py-1 rounded-lg text-xs font-semibold bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 transition-all flex items-center gap-1.5">
                                    <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                                    <span x-text="webDbTesting ? 'Testing...' : 'Test Connection'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block text-slate-400 mb-1">Host <span class="text-red-400">*</span></label>
                                <input type="text" x-model="dbHost" @input="webDbTested = false; webDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Port <span class="text-red-400">*</span></label>
                                <input type="number" x-model="dbPort" @input="webDbTested = false; webDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Database Name <span class="text-red-400">*</span></label>
                                <input type="text" x-model="dbDatabase" @input="webDbTested = false; webDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Username <span class="text-red-400">*</span></label>
                                <input type="text" x-model="dbUsername" @input="webDbTested = false; webDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Password</label>
                                <input type="password" x-model="dbPassword" @input="webDbTested = false; webDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
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
                                <span class="w-2.5 h-2.5 rounded-full" :class="gameDbTested ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">2. Ragnarok Game Database</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span x-show="gameDbTested" class="text-xs text-emerald-400 font-bold flex items-center gap-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Verified
                                </span>
                                <button type="button" @click="testGameDb()" :disabled="gameDbTesting"
                                        class="px-3 py-1 rounded-lg text-xs font-semibold bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 transition-all flex items-center gap-1.5">
                                    <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                                    <span x-text="gameDbTesting ? 'Testing...' : 'Test Game DB'"></span>
                                </button>
                            </div>
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
                                <label class="block text-slate-400 mb-1">Emulator Core <span class="text-red-400">*</span></label>
                                <select x-model="roEmulator" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    <option value="rathena">rAthena</option>
                                    <option value="hercules">Hercules</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game Mode <span class="text-red-400">*</span></label>
                                <select x-model="roRenewal" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    <option :value="true">Renewal (4th Jobs / Episode 19+)</option>
                                    <option :value="false">Pre-Renewal (Classic / Transcendent)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB Host <span class="text-red-400">*</span></label>
                                <input type="text" x-model="roDbHost" @input="gameDbTested = false; gameDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB Port <span class="text-red-400">*</span></label>
                                <input type="number" x-model="roDbPort" @input="gameDbTested = false; gameDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB Name <span class="text-red-400">*</span></label>
                                <input type="text" x-model="roDbDatabase" @input="gameDbTested = false; gameDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB User <span class="text-red-400">*</span></label>
                                <input type="text" x-model="roDbUsername" @input="gameDbTested = false; gameDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Game DB Password</label>
                                <input type="password" x-model="roDbPassword" @input="gameDbTested = false; gameDbMessage = ''; statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">Password Encryption <span class="text-red-400">*</span></label>
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

                        <!-- Ragnarok Server Network Pings & Server Rates (Expandable) -->
                        <div class="border-t border-slate-800/80 pt-3">
                            <button type="button" @click="showAdvRoConfig = !showAdvRoConfig" class="text-xs font-semibold text-cyan-400 hover:text-cyan-300 flex items-center justify-between w-full">
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="network" class="w-4 h-4 text-cyan-400"></i>
                                    <span>Ragnarok Server Network Status &amp; Game Rates (Optional)</span>
                                </span>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="showAdvRoConfig ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="showAdvRoConfig" x-cloak class="mt-4 space-y-3">
                                <p class="text-[11px] text-slate-400">
                                    Configure connection ports used to display live server status and default gameplay rates.
                                </p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <label class="block text-slate-400 mb-1">Login Port</label>
                                        <input type="number" x-model="roLoginPort" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1">Char Port</label>
                                        <input type="number" x-model="roCharPort" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1">Map Port</label>
                                        <input type="number" x-model="roMapPort" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1">Base EXP Rate</label>
                                        <input type="text" x-model="roRateBase" placeholder="1000x" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1">Job EXP Rate</label>
                                        <input type="text" x-model="roRateJob" placeholder="1000x" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1">Drop Rate</label>
                                        <input type="text" x-model="roRateDrop" placeholder="100x" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 2; statusError = ''" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800/80">
                            Back
                        </button>
                        <button type="button" @click="proceedToStep4()"
                                :class="(!webDbTested || !gameDbTested) ? 'opacity-60 cursor-not-allowed bg-slate-700 text-slate-400' : 'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/20'"
                                class="px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all">
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
                            Choose between automated migration (standard/local) or direct phpMyAdmin SQL import (recommended for cPanel).
                        </p>
                    </div>

                    <!-- Mode Selector -->
                    <div class="grid grid-cols-2 gap-3 bg-slate-950 p-1.5 rounded-2xl border border-slate-800">
                        <button type="button" @click="migrationMode = 'auto'"
                                :class="migrationMode === 'auto' ? 'bg-cyan-500 text-slate-950 font-bold shadow-md' : 'text-slate-400 hover:text-white'"
                                class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                            <span>Automated (CLI / Local / VPS)</span>
                        </button>
                        <button type="button" @click="migrationMode = 'manual_cpanel'"
                                :class="migrationMode === 'manual_cpanel' ? 'bg-amber-400 text-slate-950 font-bold shadow-md' : 'text-slate-400 hover:text-white'"
                                class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                            <i data-lucide="server" class="w-4 h-4"></i>
                            <span>cPanel / phpMyAdmin SQL Import</span>
                        </button>
                    </div>

                    <!-- Mode A: Automated -->
                    <div x-show="migrationMode === 'auto'" class="bg-slate-950/80 border border-slate-800 rounded-2xl p-6 text-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center mx-auto text-cyan-400">
                            <i data-lucide="database-zap" class="w-8 h-8" :class="migrating ? 'animate-pulse' : ''"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Execute Automated Provisioning</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                                Executes all migrations and seeds directly via PHP into <strong class="text-cyan-400" x-text="dbDatabase"></strong>.
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

                    <!-- Mode B: cPanel / phpMyAdmin Manual SQL Import -->
                    <div x-show="migrationMode === 'manual_cpanel'" class="bg-slate-950/80 border border-amber-500/30 rounded-2xl p-6 space-y-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0">
                                <i data-lucide="file-code" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white">cPanel &amp; phpMyAdmin Manual SQL Import</h3>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Avoid timeouts and PHP restrictions by importing the pre-generated SQL files directly into phpMyAdmin.
                                </p>
                            </div>
                        </div>

                        <!-- Download SQL Buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <a href="{{ route('install.download-sql', 'schema') }}"
                               class="p-3 rounded-xl bg-slate-900 border border-slate-800 hover:border-cyan-500/40 text-left flex items-center justify-between group transition-all">
                                <div>
                                    <span class="text-xs font-bold text-white group-hover:text-cyan-300 block">1. aether_schema.sql</span>
                                    <span class="text-[10px] text-slate-400 block">All tables, indexes &amp; migrations</span>
                                </div>
                                <i data-lucide="download" class="w-4 h-4 text-cyan-400 group-hover:scale-110 transition-transform"></i>
                            </a>
                            <a href="{{ route('install.download-sql', 'seeders') }}"
                               class="p-3 rounded-xl bg-slate-900 border border-slate-800 hover:border-amber-500/40 text-left flex items-center justify-between group transition-all">
                                <div>
                                    <span class="text-xs font-bold text-white group-hover:text-amber-300 block">2. aether_seeders.sql</span>
                                    <span class="text-[10px] text-slate-400 block">Starter settings, campaigns &amp; data</span>
                                </div>
                                <i data-lucide="download" class="w-4 h-4 text-amber-400 group-hover:scale-110 transition-transform"></i>
                            </a>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-slate-900/90 border border-slate-800/80 rounded-xl p-4 text-xs text-slate-300 space-y-2">
                            <p class="font-bold text-white">Quick phpMyAdmin Instructions:</p>
                            <ol class="list-decimal list-inside space-y-1 text-slate-400 text-[11px]">
                                <li>Open <strong>phpMyAdmin</strong> in your cPanel dashboard.</li>
                                <li>Click on your database (<code class="text-cyan-300 font-mono" x-text="dbDatabase"></code>) on the left sidebar.</li>
                                <li>Click the <strong>Import</strong> tab at the top.</li>
                                <li>Choose <strong class="text-white">aether_schema.sql</strong> and click <strong>Import</strong>.</li>
                                <li>Next, choose <strong class="text-white">aether_seeders.sql</strong> and click <strong>Import</strong>.</li>
                                <li class="text-amber-300 italic">Note: Your Ragnarok emulator tables (login, char, item_db) are managed separately by your rAthena/Hercules SQL files.</li>
                            </ol>
                        </div>

                        <!-- Verify Button -->
                        <div class="text-center pt-2">
                            <button type="button" @click="verifyManualSetup()" :disabled="verifyingManual"
                                    class="px-8 py-3 rounded-xl font-bold bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 shadow-xl shadow-amber-500/20 disabled:opacity-50 flex items-center justify-center gap-2 mx-auto transition-all">
                                <i data-lucide="check-circle" class="w-4 h-4" x-show="!verifyingManual"></i>
                                <span x-text="verifyingManual ? 'Verifying Tables...' : 'I Have Imported the SQL Files &bull; Verify &amp; Proceed'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 3; statusError = ''" :disabled="migrating || verifyingManual" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800/80">
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
                            Choose how you wish to access the AetherCP Admin Control Panel. Incomplete data is blocked.
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
                            <label class="block font-semibold text-slate-300 mb-1">
                                <span x-text="adminType === 'designate_game_account' ? 'Game Account Username' : 'Administrator Username'"></span>
                                <span class="text-red-400">*</span>
                            </label>
                            <input type="text" x-model="adminUsername" @input="statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100" placeholder="Username (min 2 chars)">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Notification Email <span class="text-red-400">*</span></label>
                            <input type="email" x-model="adminEmail" @input="statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100" placeholder="admin@yourserver.com">
                        </div>

                        <div x-show="adminType === 'create_new_account'">
                            <label class="block font-semibold text-slate-300 mb-1">Admin Password <span class="text-red-400">*</span></label>
                            <input type="password" x-model="adminPassword" @input="statusError = ''" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-100" placeholder="Minimum 6 characters">
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
                        <div class="flex justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="text-slate-500">Admin Username:</span>
                            <strong class="text-amber-400 font-mono" x-text="adminUsername"></strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Admin Password:</span>
                            <span class="font-mono text-emerald-400 font-bold select-all bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-500/30"
                                  x-text="adminType === 'create_new_account' ? (adminPassword || 'Admin1234!') : '(Your In-Game Account Password)'"></span>
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
