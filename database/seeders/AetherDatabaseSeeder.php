<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\News;
use App\Models\Download;
use App\Models\DonationPackage;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AetherDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial Admin Users (Aether Admin & Server Owner)
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Aether Administrator',
                'email' => 'admin@freynix.local',
                'password' => Hash::make('Admin1234!'),
                'account_id' => 2000001,
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'malda009'],
            [
                'name' => 'Server Owner',
                'email' => 'owner@freynix.local',
                'password' => Hash::make('Maki0913$'),
                'account_id' => 2000000,
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Default Site Settings
        $settings = [
            'server_name' => 'Freynix Ragnarok Online',
            'tagline' => 'Next-Generation Ragnarok Online Experience',
            'base_rate' => '1000x',
            'job_rate' => '1000x',
            'drop_rate' => '100x',
            'max_base_level' => '260',
            'max_job_level' => '55',
            'discord_url' => 'https://discord.gg/ragnarok',
            'client_version' => '2023-11-01',
            'allow_registration' => '1',
            'theme' => 'aether-dark',
        ];

        foreach ($settings as $key => $val) {
            SiteSetting::set($key, $val, 'general');
        }

        // 3. Initial News & Announcements
        if (News::count() === 0) {
            News::create([
                'title' => 'Welcome to AetherCP – Next-Gen Ragnarok Control Panel',
                'slug' => 'welcome-to-aethercp',
                'category' => 'announcement',
                'summary' => 'We are proud to introduce AetherCP, engineered with modern PHP, blazing-fast responsive UI, and full emulator integration.',
                'content' => "## Welcome Adventurers!\n\nAetherCP provides seamless access to your game accounts, real-time leaderboards, character unstuck tools, and item lookups.\n\n### Server Specifications:\n- **Base / Job Rates:** 1000x / 1000x\n- **Item Drop Rates:** 100x\n- **Episode:** 19 – Land of the Snow (Renewal)\n- **Max Level:** 260 / 55\n\nJoin our official community Discord to stay updated with guild sieges and weekly maintenance schedules!",
                'author_id' => $admin->id,
                'author_name' => 'Administrator',
                'is_pinned' => true,
                'is_published' => true,
                'published_at' => now(),
            ]);

            News::create([
                'title' => 'Server Maintenance & Renewal Update Notes',
                'slug' => 'server-maintenance-update-notes',
                'category' => 'update',
                'summary' => 'Review the latest balance changes, War of Emperium castle schedule, and new dungeon instances.',
                'content' => "### Update Highlights:\n1. **Trait Stats & 4th Jobs:** Full balance adjustments.\n2. **War of Emperium:** Castle schedule updated for Wednesday & Saturday nights.\n3. **Item Database:** Updated with latest instance gears and cards.",
                'author_id' => $admin->id,
                'author_name' => 'Staff',
                'is_pinned' => false,
                'is_published' => true,
                'published_at' => now()->subDay(),
            ]);
        }

        // 4. Default Downloads
        if (Download::count() === 0) {
            Download::create([
                'title' => 'Full Game Client (Recommended)',
                'category' => 'Full Client',
                'version' => 'v1.4.0',
                'file_size' => '2.8 GB',
                'url_mirror1' => 'https://mega.nz/file/freynix-client',
                'url_mirror2' => 'https://drive.google.com/freynix-client',
                'description' => 'Complete game client including full BGM, updated renewal sprites, and high-definition client executable.',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            Download::create([
                'title' => 'Lite Client Installer',
                'category' => 'Lite Client',
                'version' => 'v1.4.0',
                'file_size' => '350 MB',
                'url_mirror1' => 'https://mega.nz/file/freynix-lite',
                'url_mirror2' => 'https://drive.google.com/freynix-lite',
                'description' => 'Requires existing kRO installation. Extract into your clean Ragnarok directory.',
                'sort_order' => 2,
                'is_active' => true,
            ]);

            Download::create([
                'title' => 'Cumulative Manual Patch',
                'category' => 'Manual Patch',
                'version' => 'Patch 2026-11-20',
                'file_size' => '45 MB',
                'url_mirror1' => 'https://mega.nz/file/freynix-patch',
                'description' => 'Extract into game folder if your auto-patcher cannot connect.',
                'sort_order' => 3,
                'is_active' => true,
            ]);
        }

        // 5. Default Donation Packages
        if (DonationPackage::count() === 0) {
            DonationPackage::create([
                'name' => 'Novice Supply Tier',
                'description' => 'Great starting package for new adventurers.',
                'price' => 10.00,
                'currency' => 'USD',
                'points' => 1000,
                'bonus_points' => 50,
                'is_popular' => false,
                'sort_order' => 1,
            ]);

            DonationPackage::create([
                'name' => 'Heroic Champion Tier',
                'description' => 'Most popular package with 15% bonus Cash Points.',
                'price' => 25.00,
                'currency' => 'USD',
                'points' => 2500,
                'bonus_points' => 375,
                'is_popular' => true,
                'sort_order' => 2,
            ]);

            DonationPackage::create([
                'name' => 'Mythic Lord Tier',
                'description' => 'Maximum value package with 25% bonus Cash Points.',
                'price' => 50.00,
                'currency' => 'USD',
                'points' => 5000,
                'bonus_points' => 1250,
                'is_popular' => false,
                'sort_order' => 3,
            ]);
        }
    }
}

