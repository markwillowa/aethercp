<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Aether Prestige Theme Configuration
    |--------------------------------------------------------------------------
    | Flagship commercial theme for AetherCP.
    */
    'name' => 'Aether Prestige',
    'version' => '1.0.0',
    'tagline' => 'Elevate Your World. Define Your Legacy.',

    'hero' => [
        'title' => env('PRESTIGE_HERO_TITLE', 'MYTHERA'),
        'subtitle' => env('PRESTIGE_HERO_SUBTITLE', 'The Lost Realms'),
        'description' => 'An epic open-world fantasy MMORPG where ancient powers clash, kingdoms fall, and heroes rise.',
        'bg_image' => 'images/hero-bg.jpg',
    ],

    'trailers' => [
        [
            'id' => '01',
            'title' => 'The Awakening',
            'subtitle' => 'Cinematic Launch Trailer • 4K',
            'image' => 'images/trailer-1.jpg',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
        ],
        [
            'id' => '02',
            'title' => 'Shadows & Blades',
            'subtitle' => 'Assassins & Infiltrators Showcase',
            'image' => 'images/trailer-2.jpg',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
        ],
        [
            'id' => '03',
            'title' => 'War of Emperium',
            'subtitle' => 'Grand Castle Sieges & GvG',
            'image' => 'images/trailer-3.jpg',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
        ],
    ],

    'colors' => [
        'primary' => '#C6A76A',
        'navy' => '#101725',
        'surface' => '#151B26',
        'elevated' => '#1B2432',
        'platinum' => '#D9DEE7',
        'cyan' => '#2DD4BF',
    ],
];

