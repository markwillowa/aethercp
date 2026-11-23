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

        return view('install.index', [
            'requirements' => $requirements,
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
            'app_name' => 'required|string|max:50',
            'app_url' => 'required|string',
            'server_name' => 'required|string|max:50',
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
            'ro_db_host' => 'required|string',
            'ro_db_port' => 'required|numeric',
            'ro_db_database' => 'required|string',
            'ro_db_username' => 'required|string',
            'ro_db_password' => 'nullable|string',
            'ro_emulator' => 'required|in:rathena,hercules',
            'ro_renewal' => 'required|boolean',
            'ro_password_hash' => 'required|in:plain,md5,sha256',
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

            // Update site setting for server name
            SiteSetting::set('server_name', $validated['server_name']);

            return response()->json([
                'success' => true,
                'message' => 'Environment configured and web database tables migrated successfully!',
                'migration_log' => $migrateOutput,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Setup failed: ' . $e->getMessage(),
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
            'username' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'password' => 'nullable|string|min:6',
        ]);

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
