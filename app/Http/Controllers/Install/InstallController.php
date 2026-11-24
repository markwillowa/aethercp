<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use PDO;

class InstallController extends Controller
{
    /**
     * Show the installation wizard.
     */
    public function index(): View
    {
        $requirements = $this->getSystemRequirements();
        $suggestedUrl = url('/');
        // Normalize out public if somehow present
        $suggestedUrl = preg_replace('/\/public\/?$/', '', $suggestedUrl);

        $defaultConfig = [
            'app_name' => config('app.name', 'AetherCP'),
            'server_name' => config('aether.server_name', 'FreynixRO'),
            'app_url' => $suggestedUrl,
            'db_host' => config('database.connections.mysql.host', '127.0.0.1'),
            'db_port' => config('database.connections.mysql.port', 3306),
            'db_database' => config('database.connections.mysql.database', 'aether_cp'),
            'db_username' => config('database.connections.mysql.username', 'admin'),
            'db_password' => config('database.connections.mysql.password', ''),
            'ro_db_host' => config('database.connections.ragnarok.host', '127.0.0.1'),
            'ro_db_port' => config('database.connections.ragnarok.port', 3306),
            'ro_db_database' => config('database.connections.ragnarok.database', 'freynix'),
            'ro_db_username' => config('database.connections.ragnarok.username', 'admin'),
            'ro_db_password' => config('database.connections.ragnarok.password', ''),
            'ro_emulator' => config('emulator.driver', 'rathena'),
            'ro_renewal' => (bool)config('emulator.renewal', true),
            'ro_password_hash' => config('emulator.hash', 'plain'),
            'ro_login_ip' => config('emulator.servers.login.host', '127.0.0.1'),
            'ro_login_port' => (int) config('emulator.servers.login.port', 6900),
            'ro_char_ip' => config('emulator.servers.char.host', '127.0.0.1'),
            'ro_char_port' => (int) config('emulator.servers.char.port', 6121),
            'ro_map_ip' => config('emulator.servers.map.host', '127.0.0.1'),
            'ro_map_port' => (int) config('emulator.servers.map.port', 5121),
            'ro_rate_base' => config('aether.rates.base', '1000x'),
            'ro_rate_job' => config('aether.rates.job', '1000x'),
            'ro_rate_drop' => config('aether.rates.drop', '100x'),
            'ro_max_level' => (int) config('aether.rates.max_base_level', 260),
        ];

        return view('install.index', [
            'requirements' => $requirements,
            'defaultConfig' => $defaultConfig,
            'suggestedUrl' => $suggestedUrl,
            'isInstalled' => file_exists(storage_path('installed')),
        ]);
    }

    /**
     * API: Get detailed system requirements check.
     */
    public function checkRequirements(): JsonResponse
    {
        $requirements = $this->getSystemRequirements();
        return response()->json($requirements);
    }

    /**
     * API: Test Web Database Connection.
     */
    public function testDatabase(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $host = $validated['db_host'];
            $port = $validated['db_port'];
            $database = $validated['db_database'];
            $username = $validated['db_username'];
            $password = $validated['db_password'] ?? '';

            // Test server connectivity
            $pdo = new PDO(
                "mysql:host={$host};port={$port}",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 5,
                ]
            );

            // Check if database exists, if not create it
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($database));
            $exists = $stmt->fetch();

            if (!$exists) {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $message = "Database '{$database}' was created successfully!";
            } else {
                $message = "Connected successfully to database '{$database}'.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'database_exists' => (bool)$exists,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * API: Test Ragnarok Emulator Game Database Connection.
     */
    public function testGameDatabase(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ro_db_host' => 'required|string',
            'ro_db_port' => 'required|numeric',
            'ro_db_database' => 'required|string',
            'ro_db_username' => 'required|string',
            'ro_db_password' => 'nullable|string',
            'ro_emulator' => 'required|in:rathena,hercules',
        ]);

        try {
            $host = $validated['ro_db_host'];
            $port = $validated['ro_db_port'];
            $database = $validated['ro_db_database'];
            $username = $validated['ro_db_username'];
            $password = $validated['ro_db_password'] ?? '';

            $pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 5,
                ]
            );

            // Check if login table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'login'");
            $hasLoginTable = (bool)$stmt->fetch();

            // Check if char table exists
            $stmt2 = $pdo->query("SHOW TABLES LIKE 'char'");
            $hasCharTable = (bool)$stmt2->fetch();

            if (!$hasLoginTable) {
                return response()->json([
                    'success' => true,
                    'warning' => true,
                    'message' => "Connected to database '{$database}', but the 'login' table was not found. Please ensure you have imported the official rAthena/Hercules SQL files into this database.",
                    'has_login' => false,
                    'has_char' => $hasCharTable,
                ]);
            }

            // Count existing accounts
            $countStmt = $pdo->query("SELECT count(*) as total FROM `login`");
            $accountCount = $countStmt->fetchColumn();

            return response()->json([
                'success' => true,
                'warning' => false,
                'message' => "Verified game database successfully! Found {$accountCount} existing game accounts in 'login'.",
                'has_login' => true,
                'has_char' => $hasCharTable,
                'account_count' => (int)$accountCount,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Game DB connection failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * API: Save .env settings, run migrations, and run seeds.
     */
    public function setupEnvironment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|min:2|max:50',
            'app_url' => 'required|string|url',
            'server_name' => 'required|string|min:2|max:50',
            'db_host' => 'required|string',
            'db_port' => 'required|integer|between:1,65535',
            'db_database' => 'required|string|min:1',
            'db_username' => 'required|string|min:1',
            'db_password' => 'nullable|string',
            'ro_db_host' => 'required|string',
            'ro_db_port' => 'required|integer|between:1,65535',
            'ro_db_database' => 'required|string|min:1',
            'ro_db_username' => 'required|string|min:1',
            'ro_db_password' => 'nullable|string',
            'ro_emulator' => 'required|in:rathena,hercules',
            'ro_renewal' => 'required|boolean',
            'ro_password_hash' => 'required|in:plain,md5,sha256,bcrypt',
            'ro_login_ip' => 'nullable|string',
            'ro_login_port' => 'nullable|integer|between:1,65535',
            'ro_char_ip' => 'nullable|string',
            'ro_char_port' => 'nullable|integer|between:1,65535',
            'ro_map_ip' => 'nullable|string',
            'ro_map_port' => 'nullable|integer|between:1,65535',
            'ro_rate_base' => 'nullable|string|max:20',
            'ro_rate_job' => 'nullable|string|max:20',
            'ro_rate_drop' => 'nullable|string|max:20',
            'ro_max_level' => 'nullable|integer|min:1|max:999',
        ]);

        // Pre-validate database connectivity before writing configuration
        try {
            new PDO(
                "mysql:host={$validated['db_host']};port={$validated['db_port']};dbname={$validated['db_database']}",
                $validated['db_username'],
                $validated['db_password'] ?? '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Web database connection check failed: ' . $e->getMessage(),
            ], 422);
        }

        try {
            new PDO(
                "mysql:host={$validated['ro_db_host']};port={$validated['ro_db_port']};dbname={$validated['ro_db_database']}",
                $validated['ro_db_username'],
                $validated['ro_db_password'] ?? '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ragnarok game database connection check failed: ' . $e->getMessage(),
            ], 422);
        }

        try {
            // 1. Update .env file
            $envPath = base_path('.env');
            if (!File::exists($envPath)) {
                if (File::exists(base_path('.env.example'))) {
                    File::copy(base_path('.env.example'), $envPath);
                } else {
                    File::put($envPath, '');
                }
            }

            $envUpdates = [
                'APP_NAME' => '"' . $validated['app_name'] . '"',
                'APP_URL' => $validated['app_url'],
                'DB_HOST' => $validated['db_host'],
                'DB_PORT' => $validated['db_port'],
                'DB_DATABASE' => $validated['db_database'],
                'DB_USERNAME' => $validated['db_username'],
                'DB_PASSWORD' => '"' . ($validated['db_password'] ?? '') . '"',
                'RO_DB_HOST' => $validated['ro_db_host'],
                'RO_DB_PORT' => $validated['ro_db_port'],
                'RO_DB_DATABASE' => $validated['ro_db_database'],
                'RO_DB_USERNAME' => $validated['ro_db_username'],
                'RO_DB_PASSWORD' => '"' . ($validated['ro_db_password'] ?? '') . '"',
                'RO_SERVER_NAME' => '"' . $validated['server_name'] . '"',
                'RO_EMULATOR' => $validated['ro_emulator'],
                'RO_RENEWAL' => $validated['ro_renewal'] ? 'true' : 'false',
                'RO_PASSWORD_HASH' => $validated['ro_password_hash'],
                'RO_LOGIN_IP' => $validated['ro_login_ip'] ?? '127.0.0.1',
                'RO_LOGIN_PORT' => $validated['ro_login_port'] ?? 6900,
                'RO_CHAR_IP' => $validated['ro_char_ip'] ?? '127.0.0.1',
                'RO_CHAR_PORT' => $validated['ro_char_port'] ?? 6121,
                'RO_MAP_IP' => $validated['ro_map_ip'] ?? '127.0.0.1',
                'RO_MAP_PORT' => $validated['ro_map_port'] ?? 5121,
                'RO_RATE_BASE' => '"' . ($validated['ro_rate_base'] ?? '1000x') . '"',
                'RO_RATE_JOB' => '"' . ($validated['ro_rate_job'] ?? '1000x') . '"',
                'RO_RATE_DROP' => '"' . ($validated['ro_rate_drop'] ?? '100x') . '"',
                'RO_MAX_BASE_LEVEL' => $validated['ro_max_level'] ?? 260,
            ];

            $this->updateEnvFile($envPath, $envUpdates);

            // 2. Clear config caches so runtime uses the new credentials
            Artisan::call('config:clear');

            // Set DB config dynamically for immediate migration in this turn
            config([
                'database.connections.mysql.host' => $validated['db_host'],
                'database.connections.mysql.port' => $validated['db_port'],
                'database.connections.mysql.database' => $validated['db_database'],
                'database.connections.mysql.username' => $validated['db_username'],
                'database.connections.mysql.password' => $validated['db_password'] ?? '',
                'database.connections.ragnarok.host' => $validated['ro_db_host'],
                'database.connections.ragnarok.port' => $validated['ro_db_port'],
                'database.connections.ragnarok.database' => $validated['ro_db_database'],
                'database.connections.ragnarok.username' => $validated['ro_db_username'],
                'database.connections.ragnarok.password' => $validated['ro_db_password'] ?? '',
            ]);
            DB::purge('mysql');
            DB::purge('ragnarok');

            // 3. Generate APP_KEY if empty
            if (empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // 4. Run database migrations for AetherCP tables
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = trim(Artisan::output());

            // 5. Seed default configuration and starter data
            if (class_exists(\Database\Seeders\AetherDatabaseSeeder::class)) {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\AetherDatabaseSeeder',
                    '--force' => true,
                ]);
            }

            // Update site settings
            SiteSetting::set('server_name', $validated['server_name']);
            if (!empty($validated['ro_rate_base'])) {
                SiteSetting::set('base_rate', $validated['ro_rate_base']);
            }
            if (!empty($validated['ro_rate_job'])) {
                SiteSetting::set('job_rate', $validated['ro_rate_job']);
            }
            if (!empty($validated['ro_rate_drop'])) {
                SiteSetting::set('drop_rate', $validated['ro_rate_drop']);
            }
            if (!empty($validated['ro_max_level'])) {
                SiteSetting::set('max_base_level', (string) $validated['ro_max_level']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Environment configured and web database tables migrated successfully!',
                'migration_log' => $migrateOutput,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Automated migration failed: ' . $e->getMessage() . '. If your server restricts CLI/exec processes (common on cPanel), you can import the provided SQL files directly via phpMyAdmin.',
                'cpanel_fallback' => true,
            ], 500);
        }
    }

    /**
     * Download SQL dump files for manual phpMyAdmin import (cPanel support).
     */
    public function downloadSql(string $type)
    {
        $file = match ($type) {
            'schema' => base_path('database/sql/aether_schema.sql'),
            'seeders' => base_path('database/sql/aether_seeders.sql'),
            default => null,
        };

        if (!$file || !File::exists($file)) {
            abort(404, 'SQL file not found.');
        }

        return response()->download($file, basename($file), [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * API: Save .env settings and verify manually imported tables in phpMyAdmin (cPanel mode).
     */
    public function verifyManualSql(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|min:2|max:50',
            'app_url' => 'required|string|url',
            'server_name' => 'required|string|min:2|max:50',
            'db_host' => 'required|string',
            'db_port' => 'required|integer|between:1,65535',
            'db_database' => 'required|string|min:1',
            'db_username' => 'required|string|min:1',
            'db_password' => 'nullable|string',
            'ro_db_host' => 'required|string',
            'ro_db_port' => 'required|integer|between:1,65535',
            'ro_db_database' => 'required|string|min:1',
            'ro_db_username' => 'required|string|min:1',
            'ro_db_password' => 'nullable|string',
            'ro_emulator' => 'required|in:rathena,hercules',
            'ro_renewal' => 'required|boolean',
            'ro_password_hash' => 'required|in:plain,md5,sha256,bcrypt',
            'ro_login_ip' => 'nullable|string',
            'ro_login_port' => 'nullable|integer|between:1,65535',
            'ro_char_ip' => 'nullable|string',
            'ro_char_port' => 'nullable|integer|between:1,65535',
            'ro_map_ip' => 'nullable|string',
            'ro_map_port' => 'nullable|integer|between:1,65535',
            'ro_rate_base' => 'nullable|string|max:20',
            'ro_rate_job' => 'nullable|string|max:20',
            'ro_rate_drop' => 'nullable|string|max:20',
            'ro_max_level' => 'nullable|integer|min:1|max:999',
        ]);

        try {
            // 1. Update .env file
            $envPath = base_path('.env');
            if (!File::exists($envPath)) {
                if (File::exists(base_path('.env.example'))) {
                    File::copy(base_path('.env.example'), $envPath);
                } else {
                    File::put($envPath, '');
                }
            }

            $envUpdates = [
                'APP_NAME' => '"' . $validated['app_name'] . '"',
                'APP_URL' => $validated['app_url'],
                'DB_HOST' => $validated['db_host'],
                'DB_PORT' => $validated['db_port'],
                'DB_DATABASE' => $validated['db_database'],
                'DB_USERNAME' => $validated['db_username'],
                'DB_PASSWORD' => '"' . ($validated['db_password'] ?? '') . '"',
                'RO_DB_HOST' => $validated['ro_db_host'],
                'RO_DB_PORT' => $validated['ro_db_port'],
                'RO_DB_DATABASE' => $validated['ro_db_database'],
                'RO_DB_USERNAME' => $validated['ro_db_username'],
                'RO_DB_PASSWORD' => '"' . ($validated['ro_db_password'] ?? '') . '"',
                'RO_SERVER_NAME' => '"' . $validated['server_name'] . '"',
                'RO_EMULATOR' => $validated['ro_emulator'],
                'RO_RENEWAL' => $validated['ro_renewal'] ? 'true' : 'false',
                'RO_PASSWORD_HASH' => $validated['ro_password_hash'],
                'RO_LOGIN_IP' => $validated['ro_login_ip'] ?? '127.0.0.1',
                'RO_LOGIN_PORT' => $validated['ro_login_port'] ?? 6900,
                'RO_CHAR_IP' => $validated['ro_char_ip'] ?? '127.0.0.1',
                'RO_CHAR_PORT' => $validated['ro_char_port'] ?? 6121,
                'RO_MAP_IP' => $validated['ro_map_ip'] ?? '127.0.0.1',
                'RO_MAP_PORT' => $validated['ro_map_port'] ?? 5121,
                'RO_RATE_BASE' => '"' . ($validated['ro_rate_base'] ?? '1000x') . '"',
                'RO_RATE_JOB' => '"' . ($validated['ro_rate_job'] ?? '1000x') . '"',
                'RO_RATE_DROP' => '"' . ($validated['ro_rate_drop'] ?? '100x') . '"',
                'RO_MAX_BASE_LEVEL' => $validated['ro_max_level'] ?? 260,
            ];

            $this->updateEnvFile($envPath, $envUpdates);

            // 2. Set DB config dynamically
            config([
                'database.connections.mysql.host' => $validated['db_host'],
                'database.connections.mysql.port' => $validated['db_port'],
                'database.connections.mysql.database' => $validated['db_database'],
                'database.connections.mysql.username' => $validated['db_username'],
                'database.connections.mysql.password' => $validated['db_password'] ?? '',
                'database.connections.ragnarok.host' => $validated['ro_db_host'],
                'database.connections.ragnarok.port' => $validated['ro_db_port'],
                'database.connections.ragnarok.database' => $validated['ro_db_database'],
                'database.connections.ragnarok.username' => $validated['ro_db_username'],
                'database.connections.ragnarok.password' => $validated['ro_db_password'] ?? '',
            ]);
            DB::purge('mysql');
            DB::purge('ragnarok');

            // 3. Verify that the essential tables exist in the database
            $requiredTables = [
                'users',
                'site_settings',
                'attendance_campaigns',
                'attendance_campaign_days',
                'attendance_campaign_items',
                'player_inbox_items',
                'badge_definitions',
                'badge_tiers',
            ];

            $missing = [];
            foreach ($requiredTables as $tbl) {
                if (!Schema::connection('mysql')->hasTable($tbl)) {
                    $missing[] = $tbl;
                }
            }

            if (!empty($missing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing database tables: ' . implode(', ', $missing) . '. Please import database/sql/aether_schema.sql and database/sql/aether_seeders.sql in phpMyAdmin first, then try again.',
                ], 422);
            }

            // 4. Update site settings
            SiteSetting::set('server_name', $validated['server_name']);
            if (!empty($validated['ro_rate_base'])) {
                SiteSetting::set('base_rate', $validated['ro_rate_base']);
            }
            if (!empty($validated['ro_rate_job'])) {
                SiteSetting::set('job_rate', $validated['ro_rate_job']);
            }
            if (!empty($validated['ro_rate_drop'])) {
                SiteSetting::set('drop_rate', $validated['ro_rate_drop']);
            }
            if (!empty($validated['ro_max_level'])) {
                SiteSetting::set('max_base_level', (string) $validated['ro_max_level']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Database tables and configuration verified successfully via manual phpMyAdmin import! Proceeding to Administrator Setup.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Setup initial Administrator Account & Complete Installation.
     */
    public function setupAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admin_type' => 'required|in:designate_game_account,create_new_account',
            'username' => 'required|string|min:2|max:50',
            'email' => 'required|email|max:100',
            'password' => 'nullable|string',
        ]);

        if ($validated['admin_type'] === 'create_new_account') {
            if (empty($validated['password']) || strlen($validated['password']) < 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'A password of at least 6 characters is required when creating a new administrator account.',
                ], 422);
            }
        }

        try {
            $username = $validated['username'];

            if ($validated['admin_type'] === 'designate_game_account') {
                // Elevate game account in rAthena/Hercules `login` table
                $gameAccount = DB::connection('ragnarok')
                    ->table('login')
                    ->where('userid', $username)
                    ->first();

                if (!$gameAccount) {
                    return response()->json([
                        'success' => false,
                        'message' => "Game account '{$username}' not found in the game database login table. Please create it or check spelling.",
                    ], 422);
                }

                // Update group_id = 99 for admin rights
                DB::connection('ragnarok')
                    ->table('login')
                    ->where('account_id', $gameAccount->account_id)
                    ->update(['group_id' => 99]);

                // Sync with local User table
                $user = User::updateOrCreate(
                    ['account_id' => $gameAccount->account_id],
                    [
                        'username' => $gameAccount->userid,
                        'name' => $gameAccount->userid,
                        'email' => $gameAccount->email ?? ($gameAccount->userid . '@local.server'),
                        'role' => 'admin',
                    ]
                );
            } else {
                // Create dedicated web user record
                if (empty($validated['password'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A password is required when creating a new admin account.',
                    ], 422);
                }

                $user = User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $username,
                        'email' => $validated['email'] ?? ($username . '@aethercp.local'),
                        'password' => Hash::make($validated['password']),
                        'role' => 'admin',
                        'account_id' => 1,
                    ]
                );
            }

            // 6. Write installation lock file
            $lockData = [
                'installed_at' => now()->toIso8601String(),
                'version' => config('aether.version', '1.0.0'),
                'admin_account' => $username,
            ];
            File::put(storage_path('installed'), json_encode($lockData, JSON_PRETTY_PRINT));
            SiteSetting::set('installed', 'true');

            // Clear view and optimize caches
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');

            return response()->json([
                'success' => true,
                'message' => "Administrator account '{$username}' configured successfully! AetherCP installation complete.",
                'redirect' => route('home'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Admin setup failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gather system requirements and status.
     */
    protected function getSystemRequirements(): array
    {
        $phpVersion = PHP_VERSION;
        $phpPass = version_compare($phpVersion, '8.2.0', '>=');

        $requiredExtensions = [
            'pdo' => 'PDO Data Objects',
            'pdo_mysql' => 'MySQL PDO Driver',
            'openssl' => 'OpenSSL Security',
            'mbstring' => 'Multibyte String',
            'tokenizer' => 'Tokenizer Support',
            'xml' => 'XML Parser',
            'ctype' => 'Ctype Character Checking',
            'json' => 'JSON Serializer',
            'fileinfo' => 'File Information',
            'curl' => 'cURL HTTP Client',
        ];

        $extensionResults = [];
        $allExtensionsPass = true;

        foreach ($requiredExtensions as $ext => $name) {
            $loaded = extension_loaded($ext);
            $extensionResults[$ext] = [
                'name' => $name,
                'loaded' => $loaded,
            ];
            if (!$loaded && in_array($ext, ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'json'])) {
                $allExtensionsPass = false;
            }
        }

        $writablePaths = [
            'storage' => storage_path(),
            'bootstrap_cache' => base_path('bootstrap/cache'),
            'env' => base_path('.env'),
        ];

        $pathResults = [];
        $allPathsPass = true;

        foreach ($writablePaths as $key => $path) {
            $isWritable = is_writable($path) || (!file_exists($path) && is_writable(dirname($path)));
            $pathResults[$key] = [
                'path' => $path,
                'writable' => $isWritable,
            ];
            if (!$isWritable) {
                $allPathsPass = false;
            }
        }

        return [
            'php' => [
                'version' => $phpVersion,
                'required' => '8.2+',
                'pass' => $phpPass,
            ],
            'extensions' => $extensionResults,
            'paths' => $pathResults,
            'all_pass' => ($phpPass && $allExtensionsPass && $allPathsPass),
        ];
    }

    /**
     * Helper to update key-value pairs in a .env file safely.
     */
    protected function updateEnvFile(string $filePath, array $values): void
    {
        $content = File::get($filePath);

        foreach ($values as $key => $value) {
            // Check if key exists in file
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        File::put($filePath, $content);
    }
}

