<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Brand & Identity
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'AetherCP'),
    'server_name' => env('RO_SERVER_NAME', 'Freynix RO'),
    'tagline' => env('AETHER_TAGLINE', 'Next-Generation Ragnarok Online Experience'),
    'description' => 'AetherCP is an independent, modern control panel engineered for high-performance Ragnarok Online communities.',
    
    /*
    |--------------------------------------------------------------------------
    | Theme & Appearance
    |--------------------------------------------------------------------------
    | Default theme is 'aether-dark', engineered with modern MMORPG aesthetics:
    | Deep navy background, cyan/blue ethereal glow, responsive layouts.
    */
    'theme' => env('AETHER_THEME', 'aether-dark'),
    'supported_themes' => [
        'aether-dark' => 'Aether Dark (Default)',
        'valkyrie-light' => 'Valkyrie Light',
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Rates & Meta
    |--------------------------------------------------------------------------
    */
    'rates' => [
        'base' => env('RO_RATE_BASE', '1000x'),
        'job' => env('RO_RATE_JOB', '1000x'),
        'drop' => env('RO_RATE_DROP', '100x'),
        'card' => env('RO_RATE_CARD', '100x'),
        'mvp' => env('RO_RATE_MVP', '10x'),
        'max_base_level' => env('RO_MAX_BASE_LEVEL', 260),
        'max_job_level' => env('RO_MAX_JOB_LEVEL', 55),
        'max_stats' => env('RO_MAX_STATS', 130),
        'max_aspd' => env('RO_MAX_ASPD', 193),
    ],

    /*
    |--------------------------------------------------------------------------
    | Social & Community Links
    |--------------------------------------------------------------------------
    */
    'community' => [
        'discord' => env('AETHER_DISCORD_URL', 'https://discord.gg/'),
        'facebook' => env('AETHER_FACEBOOK_URL', ''),
        'youtube' => env('AETHER_YOUTUBE_URL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Client Downloads Defaults
    |--------------------------------------------------------------------------
    */
    'downloads' => [
        'full_client' => [
            'name' => 'Full Game Client (Recommended)',
            'size' => '2.8 GB',
            'version' => 'v1.4.0',
            'url' => 'https://mega.nz/',
        ],
        'lite_client' => [
            'name' => 'Lite Client Installer',
            'size' => '320 MB',
            'version' => 'v1.4.0',
            'url' => 'https://drive.google.com/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Intervals (in seconds)
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'server_status' => env('AETHER_CACHE_STATUS', 60), // 1 minute
        'rankings' => env('AETHER_CACHE_RANKINGS', 300), // 5 minutes
        'emblems' => env('AETHER_CACHE_EMBLEMS', 86400), // 24 hours
        'item_db' => env('AETHER_CACHE_ITEMS', 3600), // 1 hour
    ],
];

