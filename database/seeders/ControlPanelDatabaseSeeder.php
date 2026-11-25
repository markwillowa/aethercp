<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ControlPanelDatabaseSeeder extends Seeder
{
    /**
     * Seed starter data for the AetherCP Control Panel web database.
     */
    public function run(?array $modules = null): void
    {
        $this->call(AetherDatabaseSeeder::class, false, ['modules' => $modules]);
    }
}
