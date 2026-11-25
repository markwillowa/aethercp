<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's databases (Control Panel Web DB & Ragnarok Game DB).
     */
    public function run(): void
    {
        // 1. Seed Control Panel Web Database
        $this->call(ControlPanelDatabaseSeeder::class);

        // 2. Seed Ragnarok Game Database (if connection configured)
        $this->call(RagnarokDatabaseSeeder::class);
    }
}
