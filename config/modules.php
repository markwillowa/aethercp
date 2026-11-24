<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Configuration & Feature Flags
    |--------------------------------------------------------------------------
    | Each module can be individually enabled or disabled by server admins.
    */
    'modules' => [
        'account' => [
            'name' => 'Account System',
            'description' => 'Registration, login, password recovery, profile and security.',
            'version' => '1.0.0',
            'enabled' => true,
            'protected' => true, // Essential core module
        ],
        'character' => [
            'name' => 'Character Profiles',
            'description' => 'Character viewer, stats, equipment, and unstuck tools.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'rankings' => [
            'name' => 'Rankings & Leaderboards',
            'description' => 'Base/Job level, Zeny, MVP kills, Guild, and PvP leaderboards.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'guild' => [
            'name' => 'Guild Directory',
            'description' => 'Guild rosters, castles, and emblem rendering.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'server_info' => [
            'name' => 'Server Information & Status',
            'description' => 'Live server status, rates, and online player tracking.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'itemdb' => [
            'name' => 'Item Database',
            'description' => 'Searchable item directory with sprites and descriptions.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'news' => [
            'name' => 'News & CMS',
            'description' => 'Patch notes, server announcements, and event posts.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'downloads' => [
            'name' => 'Client Downloads',
            'description' => 'Download links and mirror management for players.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'donations' => [
            'name' => 'Donation System',
            'description' => 'Donation packages, reward credit logs, and verification.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'marketplace' => [
            'name' => 'Player Marketplace',
            'description' => 'Player-to-player trade house for escrowed items using Zeny or Cash Points.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
        'support' => [
            'name' => 'Support Center & Tickets',
            'description' => 'Two-way player support ticket management system with attachments and staff assignment.',
            'version' => '1.0.0',
            'enabled' => true,
        ],
    ],
];

