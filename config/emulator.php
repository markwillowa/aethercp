<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Emulator Selection
    |--------------------------------------------------------------------------
    | Supported: 'rathena', 'hercules'
    */
    'driver' => env('RO_EMULATOR', 'rathena'),

    /*
    |--------------------------------------------------------------------------
    | Renewal or Pre-Renewal
    |--------------------------------------------------------------------------
    | Controls stat fields (traits: pow, sta, wis, etc.), job tiers, and formulas.
    */
    'renewal' => env('RO_RENEWAL', true),

    /*
    |--------------------------------------------------------------------------
    | Password Hashing Method
    |--------------------------------------------------------------------------
    | Supported: 'md5', 'bcrypt', 'sha256', 'plain'
    | Auto-detection is used during login verification to ensure zero lockouts.
    */
    'password_hash' => env('RO_PASSWORD_HASH', 'md5'),

    /*
    |--------------------------------------------------------------------------
    | Account Registration Defaults
    |--------------------------------------------------------------------------
    */
    'registration' => [
        'min_username' => 4,
        'max_username' => 23,
        'min_password' => 6,
        'max_password' => 32,
        'allowed_chars' => '/^[a-zA-Z0-9_]+$/',
        'default_group_id' => 0,
        'default_char_slots' => 9,
        'require_email_verification' => false,
        'allow_duplicate_emails' => false,
        'allow_username_in_password' => false,
        'case_sensitive_username' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Network Pings
    |--------------------------------------------------------------------------
    */
    'servers' => [
        'login' => [
            'name' => 'Login Server',
            'host' => env('RO_LOGIN_IP', '127.0.0.1'),
            'port' => (int) env('RO_LOGIN_PORT', 6900),
            'timeout' => 1.5,
        ],
        'char' => [
            'name' => 'Character Server',
            'host' => env('RO_CHAR_IP', '127.0.0.1'),
            'port' => (int) env('RO_CHAR_PORT', 6121),
            'timeout' => 1.5,
        ],
        'map' => [
            'name' => 'Map Server',
            'host' => env('RO_MAP_IP', '127.0.0.1'),
            'port' => (int) env('RO_MAP_PORT', 5121),
            'timeout' => 1.5,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Emulator Table Names
    |--------------------------------------------------------------------------
    | Configurable table names allowing seamless custom emulator schemas.
    */
    'tables' => [
        'login' => 'login',
        'char' => 'char',
        'guild' => 'guild',
        'guild_member' => 'guild_member',
        'guild_castle' => 'guild_castle',
        'guild_storage' => 'guild_storage',
        'inventory' => 'inventory',
        'storage' => 'storage',
        'cart_inventory' => 'cart_inventory',
        'item_db' => 'item_db',
        'item_db2' => 'item_db2',
        'party' => 'party',
        'charlog' => 'charlog',
        'interlog' => 'interlog',
    ],

    /*
    |--------------------------------------------------------------------------
    | GM Account Threshold
    |--------------------------------------------------------------------------
    | Accounts with group_id >= this value are treated as Staff/GM
    | and can be excluded from public rankings and player lists for privacy.
    */
    'gm_level_threshold' => env('RO_GM_LEVEL_THRESHOLD', 1),
    'hide_gm_from_rankings' => true,
    'hide_gm_from_online' => true,
];

