<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AetherInstallCommand extends Command
{
    protected $signature = 'aether:install {--force : Force the installation without confirmation}';
    protected $description = 'Setup and initialize AetherCP database, seeders, and assets';

    public function handle(): int
    {
        $this->info("==========================================");
        $this->info("      AETHERCP - INSTALLATION WIZARD     ");
        $this->info("==========================================");

        // 1. Verify Application Database Connection
        $this->line("1. Checking AetherCP Database Connection...");
        try {
            DB::connection()->getPdo();
            $this->info("   [OK] AetherCP web database connected.");
        } catch (\Throwable $e) {
            $this->error("   [FAILED] Could not connect to AetherCP web database: " . $e->getMessage());
            return 1;
        }

        // 2. Verify Game Server Database Connection
        $this->line("2. Checking Ragnarok Emulator Database Connection...");
        try {
            DB::connection('ragnarok')->getPdo();
            $this->info("   [OK] Ragnarok game database connected.");
        } catch (\Throwable $e) {
            $this->warn("   [WARNING] Ragnarok game database could not be reached: " . $e->getMessage());
            $this->line("   Please verify RO_DB_* credentials in your .env file.");
        }

        // 3. Run Migrations
        $this->line("3. Running database migrations...");
        Artisan::call('migrate', ['--force' => true], $this->output);

        // 4. Run Seeders
        $this->line("4. Populating initial data and default administrator...");
        Artisan::call('db:seed', ['--class' => 'AetherDatabaseSeeder', '--force' => true], $this->output);

        // 5. Success summary
        $this->info("\n==========================================");
        $this->info("      AETHERCP SUCCESSFULLY INSTALLED!    ");
        $this->info("==========================================");
        $this->line("Default Admin Credentials:");
        $this->line("  Username: admin");
        $this->line("  Password: Admin1234!");
        $this->line("  URL: " . config('app.url') . "/login");
        $this->newLine();

        return 0;
    }
}

