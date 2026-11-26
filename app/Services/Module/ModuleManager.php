<?php

namespace App\Services\Module;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ModuleManager
{
    protected string $modulesPath;
    protected ?string $lastMigrationNote = null;

    public function __construct()
    {
        $this->modulesPath = base_path('modules');
    }

    public function getModules(): array
    {
        // 1. Core modules defined in configuration
        $configured = config('modules.modules', []);
        $modules = [];

        foreach ($configured as $key => $mod) {
            $enabledSetting = SiteSetting::get("module_enabled_{$key}", null);
            if ($key === 'database' && $enabledSetting === null) {
                $enabledSetting = SiteSetting::get("module_enabled_itemdb", null);
            }
            $isEnabled = $enabledSetting !== null ? (bool) $enabledSetting : (bool) ($mod['enabled'] ?? true);

            $modules[$key] = array_merge($mod, [
                'key' => $key,
                'is_core' => true,
                'enabled' => $isEnabled,
                'has_update' => false,
            ]);
        }

        // 2. Discover file-based custom extensions safely
        if (File::isDirectory($this->modulesPath)) {
            $dirs = File::directories($this->modulesPath);
            foreach ($dirs as $dir) {
                $key = basename($dir);
                $manifestPath = $dir . DIRECTORY_SEPARATOR . 'module.json';

                if (File::exists($manifestPath)) {
                    $manifest = json_decode(File::get($manifestPath), true) ?: [];
                    $enabledSetting = SiteSetting::get("module_enabled_{$key}", null);
                    $isEnabled = $enabledSetting !== null ? (bool) $enabledSetting : false;

                    $modules[$key] = [
                        'key' => $key,
                        'name' => $manifest['name'] ?? ucfirst($key),
                        'description' => $manifest['description'] ?? 'Extension Module',
                        'version' => $manifest['version'] ?? '1.0.0',
                        'author' => $manifest['author'] ?? 'Community Developer',
                        'is_core' => false,
                        'enabled' => $isEnabled,
                        'dependencies' => $manifest['dependencies'] ?? [],
                    ];
                }
            }
        }

        return $modules;
    }

    public function isEnabled(string $key): bool
    {
        $modules = $this->getModules();
        if ($key === 'itemdb' || $key === 'database') {
            if (isset($modules['database'])) {
                return (bool) $modules['database']['enabled'];
            }
            if (isset($modules['itemdb'])) {
                return (bool) $modules['itemdb']['enabled'];
            }
            return true;
        }

        return isset($modules[$key]) && (bool) $modules[$key]['enabled'];
    }

    public function toggle(string $key): bool
    {
        $modules = $this->getModules();
        if (!isset($modules[$key])) {
            if ($key === 'itemdb' && isset($modules['database'])) {
                $key = 'database';
            } elseif ($key === 'database' && isset($modules['itemdb'])) {
                $key = 'itemdb';
            } else {
                return false;
            }
        }

        // Prevent disabling protected core modules like account system
        if (!empty($modules[$key]['protected'])) {
            return false;
        }

        $currentState = (bool) $modules[$key]['enabled'];
        $newState = !$currentState;
        $this->lastMigrationNote = null;

        if ($newState) {
            $this->lastMigrationNote = $this->ensureModuleMigrated($key);
        }

        SiteSetting::set("module_enabled_{$key}", $newState ? '1' : '0');
        if ($key === 'database') {
            SiteSetting::set("module_enabled_itemdb", $newState ? '1' : '0');
        } elseif ($key === 'itemdb') {
            SiteSetting::set("module_enabled_database", $newState ? '1' : '0');
        } elseif ($key === 'character_creation') {
            SiteSetting::set('char_create_enabled', $newState ? '1' : '0', 'character_creation');
        }

        return $newState;
    }

    public function getLastMigrationNote(): ?string
    {
        return $this->lastMigrationNote;
    }

    /**
     * Automatically run pending PHP/SQL migrations for a module when it is enabled.
     */
    public function ensureModuleMigrated(string $key): ?string
    {
        $map = [
            'news' => [
                'tables' => ['news'],
                'migrations' => ['2026_11_23_000002_create_aether_news_table'],
            ],
            'downloads' => [
                'tables' => ['downloads'],
                'migrations' => ['2026_11_23_000003_create_aether_downloads_table'],
            ],
            'donations' => [
                'tables' => ['donation_packages', 'donations'],
                'migrations' => ['2026_11_23_000004_create_aether_donations_table'],
            ],
            'marketplace' => [
                'tables' => ['marketplace_items', 'marketplace_listings', 'marketplace_transactions'],
                'migrations' => ['2026_11_24_000001_create_aether_marketplace_tables'],
            ],
            'support' => [
                'tables' => ['support_tickets', 'support_ticket_messages'],
                'migrations' => ['2026_11_24_000002_create_aether_support_ticket_tables'],
            ],
            'faq' => [
                'tables' => ['faq_categories', 'faqs'],
                'migrations' => ['2026_11_24_000003_create_aether_faq_tables'],
            ],
            'rankings' => [
                'tables' => ['ranking_character_stats', 'badge_definitions', 'reward_definitions'],
                'migrations' => [
                    '2026_11_25_000001_create_ranking_tracking_tables',
                    '2026_11_25_000002_create_badge_system_tables',
                    '2026_11_25_000003_create_reward_system_tables',
                ],
            ],
            'character' => [
                'tables' => ['player_profile_settings', 'player_achievements', 'character_service_transactions', 'character_service_logs', 'character_locks', 'character_deletion_requests', 'character_admin_notes'],
                'migrations' => ['2026_11_25_000004_create_player_profiles_tables', '2026_11_27_000002_create_character_management_tables'],
            ],
            'attendance' => [
                'tables' => ['attendance_campaigns', 'attendance_campaign_days', 'attendance_campaign_items', 'player_inbox_items'],
                'migrations' => ['2026_11_25_000006_create_attendance_and_daily_rewards_tables'],
            ],
            'cash_shop' => [
                'tables' => ['cash_shop_categories', 'cash_shop_products', 'cash_shop_purchases'],
                'migrations' => ['2026_11_26_000001_create_cash_shop_tables'],
            ],
            'forum' => [
                'tables' => ['forum_categories', 'forum_boards', 'forum_topics', 'forum_posts', 'forum_user_profiles', 'reputation_levels'],
                'migrations' => [
                    '2026_11_26_000002_create_forum_tables',
                    '2026_11_26_000003_create_forum_identity_and_reputation_tables',
                ],
            ],
            'redeem_codes' => [
                'tables' => ['redeem_campaigns', 'redeem_codes', 'redeem_code_rewards', 'redeem_redemptions', 'redeem_reward_deliveries', 'redeem_campaign_logs'],
                'migrations' => ['2026_11_27_000001_create_redeem_code_tables'],
            ],
        ];

        $ranMigrations = [];

        // 1. Check custom extension module migrations directory first
        $extMigPath = $this->modulesPath . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        if (File::isDirectory($extMigPath)) {
            try {
                Artisan::call('migrate', [
                    '--path' => 'modules/' . $key . '/database/migrations',
                    '--force' => true,
                ]);
                $out = trim(Artisan::output());
                if ($out && !str_contains($out, 'Nothing to migrate')) {
                    $ranMigrations[] = $key . ' extension migrations';
                }
            } catch (\Throwable $e) {
                Log::warning("Extension migration failed for [{$key}]: " . $e->getMessage());
            }
        }

        // 2. Run pending core Laravel migrations if any module tables are missing or pending
        $spec = $map[$key] ?? null;
        $missingTables = [];
        if ($spec) {
            foreach ($spec['tables'] as $table) {
                if (!Schema::hasTable($table)) {
                    $missingTables[] = $table;
                }
            }
        }

        // Always try running pending Laravel migrations when enabling a module with migrations
        if ($spec || !empty($missingTables)) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                $out = trim(Artisan::output());
                if ($out && !str_contains($out, 'Nothing to migrate')) {
                    $ranMigrations[] = 'database migrations';
                }
            } catch (\Throwable $e) {
                Log::warning("Artisan migrate failed while enabling module [{$key}]: " . $e->getMessage());
            }
        }

        // 3. Fallback: If any expected module table is STILL missing (e.g. restricted CLI or stale migrations table),
        // import the module's SQL migration files directly on MySQL/MariaDB.
        if ($spec) {
            $stillMissing = [];
            foreach ($spec['tables'] as $table) {
                if (!Schema::hasTable($table)) {
                    $stillMissing[] = $table;
                }
            }

            if (!empty($stillMissing) && DB::connection()->getDriverName() === 'mysql') {
                foreach ($spec['migrations'] as $migrationName) {
                    $sqlPath = database_path("sql/migrations/{$migrationName}.sql");
                    if (File::exists($sqlPath)) {
                        try {
                            $sql = File::get($sqlPath);
                            // Strip transaction control statements so DB::unprepared runs cleanly
                            $sql = preg_replace('/^\s*(START TRANSACTION|COMMIT)\s*;\s*$/im', '', $sql);
                            DB::unprepared($sql);
                            $ranMigrations[] = "{$migrationName}.sql";
                        } catch (\Throwable $e) {
                            Log::error("SQL fallback migration failed for [{$migrationName}]: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        if (!empty($ranMigrations)) {
            return 'Auto-migrated: ' . implode(', ', array_unique($ranMigrations)) . '.';
        }

        return null;
    }
}

