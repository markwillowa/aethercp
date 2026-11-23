<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Aether Chronicles Theme Configuration
    |--------------------------------------------------------------------------
    */
    'name' => 'Aether Chronicles',
    'tagline' => 'Your Legend Begins in Rune-Midgard.',
    'episode_label' => 'Episode XIII: Noghaltz & The Forgotten Ruins',
    
    // Background and Header Artwork
    'assets' => [
        'sky_background' => 'images/hero-sky.jpg',
        'logo' => 'images/chronicle-logo.png',
        'seal' => 'images/chronicle-seal.png',
        'promo_banner' => 'images/promo-banner.jpg',
    ],

    // Featured Promotional Characters (Left & Right Header / Welcome)
    'characters' => [
        'maid' => 'images/characters/high_priest.png',
        'hero_left' => 'images/characters/sniper.png',
        'hero_center' => 'images/characters/lord_knight.png',
        'hero_right' => 'images/characters/high_wizard.png',
    ],

    // Video Showcase
    'multimedia' => [
        'enabled' => true,
        'title' => 'MULTIMEDIA',
        'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
    ],

    // Quick Action Buttons
    'quicklinks' => [
        ['label' => 'REGISTER ACCOUNT', 'route' => 'register', 'icon' => 'user-plus', 'color' => 'gold'],
        ['label' => 'DOWNLOAD CLIENT', 'route' => 'downloads.index', 'icon' => 'download', 'color' => 'wood'],
        ['label' => 'ITEM DATABASE', 'route' => 'itemdb.index', 'icon' => 'database', 'color' => 'stone'],
        ['label' => 'DONATION VAULT', 'route' => 'donations.index', 'icon' => 'coins', 'color' => 'amber'],
    ],

    // 6 Homepage Feature Tiles (Configurable)
    'features' => [
        [
            'title' => 'Trans & 3rd Jobs',
            'desc' => 'Completely balanced skill damage tables and renewal progression.',
            'image' => 'images/feat_basic_cards.jpg',
            'route' => 'home',
        ],
        [
            'title' => 'War of Emperium',
            'desc' => 'Scheduled castle sieges with automated guild economy rewards.',
            'image' => 'images/feat_pvp_woe.jpg',
            'route' => 'woe.index',
        ],
        [
            'title' => 'MVP & Boss Hunts',
            'desc' => 'Live boss timers, automated tomb stones, and rare drops.',
            'image' => 'images/feat_mvp_cards.jpg',
            'route' => 'itemdb.index',
        ],
        [
            'title' => 'Custom Quests',
            'desc' => 'Story-driven seasonal questlines, headgear crafts, and pet hunts.',
            'image' => 'images/feat_quests_events.jpg',
            'route' => 'news.index',
        ],
        [
            'title' => 'Hall of Fame',
            'desc' => 'Live ranking ladder tracking Top Level, MVP Kills, and Zeny.',
            'image' => 'images/feat_basic_cards.jpg',
            'route' => 'rankings.index',
        ],
        [
            'title' => 'Active Guilds',
            'desc' => 'Join elite alliances and conquer Rune-Midgard territory.',
            'image' => 'images/feat_pvp_woe.jpg',
            'route' => 'guilds.index',
        ],
    ],
];
