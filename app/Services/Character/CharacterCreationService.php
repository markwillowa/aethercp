<?php

namespace App\Services\Character;

use App\Contracts\EmulatorInterface;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Emulator\JobHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CharacterCreationService
{
    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    /**
     * Categorized job catalog using rAthena job IDs.
     */
    public function getJobCatalog(): array
    {
        return [
            'novice' => [
                'label' => 'Novice',
                'description' => 'Standard starting Novice class.',
                'jobs' => [
                    0 => 'Novice',
                ],
            ],
            'first_class' => [
                'label' => 'First Classes',
                'description' => 'Classic 1st job classes.',
                'jobs' => [
                    1 => 'Swordsman',
                    2 => 'Mage',
                    3 => 'Archer',
                    4 => 'Acolyte',
                    5 => 'Merchant',
                    6 => 'Thief',
                ],
            ],
            'second_class' => [
                'label' => 'Second Classes',
                'description' => '2-1 and 2-2 job classes.',
                'jobs' => [
                    7 => 'Knight',
                    8 => 'Priest',
                    9 => 'Wizard',
                    10 => 'Blacksmith',
                    11 => 'Hunter',
                    12 => 'Assassin',
                    14 => 'Crusader',
                    15 => 'Monk',
                    16 => 'Sage',
                    17 => 'Rogue',
                    18 => 'Alchemist',
                    19 => 'Bard',
                    20 => 'Dancer',
                ],
            ],
            'trans_first' => [
                'label' => 'Transcendent First Classes',
                'description' => 'Rebirth / High Novice and High 1st classes.',
                'jobs' => [
                    4001 => 'High Novice',
                    4002 => 'High Swordsman',
                    4003 => 'High Mage',
                    4004 => 'High Archer',
                    4005 => 'High Acolyte',
                    4006 => 'High Merchant',
                    4007 => 'High Thief',
                ],
            ],
            'trans_second' => [
                'label' => 'Transcendent Second Classes',
                'description' => 'Rebirth 2nd job classes.',
                'jobs' => [
                    4008 => 'Lord Knight',
                    4009 => 'High Priest',
                    4010 => 'High Wizard',
                    4011 => 'Whitesmith',
                    4012 => 'Sniper',
                    4013 => 'Assassin Cross',
                    4015 => 'Paladin',
                    4016 => 'Champion',
                    4017 => 'Professor',
                    4018 => 'Stalker',
                    4019 => 'Creator',
                    4020 => 'Clown',
                    4021 => 'Gypsy',
                ],
            ],
            'third_class' => [
                'label' => 'Third Classes',
                'description' => 'Renewal 3rd job classes.',
                'jobs' => [
                    4054 => 'Rune Knight',
                    4055 => 'Warlock',
                    4056 => 'Ranger',
                    4057 => 'Arch Bishop',
                    4058 => 'Mechanic',
                    4059 => 'Guillotine Cross',
                    4060 => 'Royal Guard',
                    4061 => 'Sorcerer',
                    4062 => 'Minstrel',
                    4063 => 'Wanderer',
                    4064 => 'Sura',
                    4065 => 'Genetic',
                    4066 => 'Shadow Chaser',
                ],
            ],
            'expanded' => [
                'label' => 'Expanded Classes',
                'description' => 'Super Novice, Gunslinger, Ninja, Taekwon, and evolutions.',
                'jobs' => [
                    23 => 'Super Novice',
                    24 => 'Gunslinger',
                    25 => 'Ninja',
                    28 => 'Taekwon',
                    29 => 'Star Gladiator',
                    30 => 'Soul Linker',
                    4046 => 'Taekwon Master',
                    4047 => 'Soul Reaver',
                    4049 => 'Rebellion',
                    4096 => 'Expanded Super Novice',
                    4191 => 'Kagerou',
                    4192 => 'Oboro',
                ],
            ],
            'doram' => [
                'label' => 'Doram Classes',
                'description' => 'Lasagna Doram race classes.',
                'jobs' => [
                    4218 => 'Summoner (Doram)',
                ],
            ],
        ];
    }

    /**
     * Supported hair color palettes (rAthena standard 0..8).
     */
    public function getHairColorCatalog(): array
    {
        return [
            0 => ['name' => 'Default', 'hex' => '#e8c468'],
            1 => ['name' => 'Blonde', 'hex' => '#facc15'],
            2 => ['name' => 'Purple / Lavender', 'hex' => '#a855f7'],
            3 => ['name' => 'Brown / Chestnut', 'hex' => '#92400e'],
            4 => ['name' => 'Emerald Green', 'hex' => '#10b981'],
            5 => ['name' => 'Cobalt Blue', 'hex' => '#3b82f6'],
            6 => ['name' => 'Silver / White', 'hex' => '#e2e8f0'],
            7 => ['name' => 'Obsidian Black', 'hex' => '#1e293b'],
            8 => ['name' => 'Crimson Red', 'hex' => '#ef4444'],
        ];
    }

    /**
     * Supported presets for quick admin setup.
     */
    public function getPresets(): array
    {
        $serverMaxBase = (int) config('aether.rates.max_base_level', 260);
        $serverMaxJob = (int) config('aether.rates.max_job_level', 70);

        return [
            'classic_prerenewal' => [
                'label' => 'Classic Pre-Renewal',
                'description' => 'Novice only, Lv. 1–99 / Job 1–50, classic Prontera/Training start.',
                'settings' => [
                    'enabled_categories' => ['novice'],
                    'enabled_jobs' => [0],
                    'starting_base_level' => 1,
                    'starting_job_level' => 1,
                    'max_base_level' => min(99, $serverMaxBase),
                    'max_job_level' => min(50, $serverMaxJob),
                    'allow_custom_levels' => false,
                    'hairstyles' => range(1, 23),
                    'hair_colors' => range(0, 8),
                ],
            ],
            'renewal' => [
                'label' => 'Renewal',
                'description' => 'Novice start with Renewal hairstyles (1–29) and higher level cap.',
                'settings' => [
                    'enabled_categories' => ['novice'],
                    'enabled_jobs' => [0],
                    'starting_base_level' => 1,
                    'starting_job_level' => 1,
                    'max_base_level' => min(200, $serverMaxBase),
                    'max_job_level' => min(70, $serverMaxJob),
                    'allow_custom_levels' => false,
                    'hairstyles' => range(1, 29),
                    'hair_colors' => range(0, 8),
                ],
            ],
            'transcendent' => [
                'label' => 'Transcendent Server',
                'description' => 'Start directly as High Novice or High First Classes (4001–4007).',
                'settings' => [
                    'enabled_categories' => ['trans_first'],
                    'enabled_jobs' => [4001, 4002, 4003, 4004, 4005, 4006, 4007],
                    'starting_base_level' => 1,
                    'starting_job_level' => 1,
                    'max_base_level' => min(99, $serverMaxBase),
                    'max_job_level' => min(50, $serverMaxJob),
                    'allow_custom_levels' => false,
                    'hairstyles' => range(1, 29),
                    'hair_colors' => range(0, 8),
                ],
            ],
            'third_job' => [
                'label' => 'Third Job Server',
                'description' => 'Instant Third Class creation (4054–4066) with configurable starting levels.',
                'settings' => [
                    'enabled_categories' => ['third_class'],
                    'enabled_jobs' => [4054, 4055, 4056, 4057, 4058, 4059, 4060, 4061, 4062, 4063, 4064, 4065, 4066],
                    'starting_base_level' => 1,
                    'starting_job_level' => 1,
                    'max_base_level' => min(175, $serverMaxBase),
                    'max_job_level' => min(60, $serverMaxJob),
                    'allow_custom_levels' => true,
                    'hairstyles' => range(1, 29),
                    'hair_colors' => range(0, 8),
                ],
            ],
            'custom' => [
                'label' => 'Custom Server',
                'description' => 'Full manual control over all job categories, levels, and starting gear.',
                'settings' => [],
            ],
        ];
    }

    /**
     * Load all character creation settings with safe defaults.
     */
    public function getSettings(): array
    {
        $serverMaxBase = max(1, (int) config('aether.rates.max_base_level', 260));
        $serverMaxJob = max(1, (int) config('aether.rates.max_job_level', 70));

        $hairstyles = json_decode((string) SiteSetting::get('char_create_hairstyles', ''), true);
        if (!is_array($hairstyles) || empty($hairstyles)) {
            $hairstyles = range(1, 29);
        }

        $hairColors = json_decode((string) SiteSetting::get('char_create_hair_colors', ''), true);
        if (!is_array($hairColors) || $hairColors === []) {
            $hairColors = range(0, 8);
        }

        $enabledCategories = json_decode((string) SiteSetting::get('char_create_enabled_categories', ''), true);
        if (!is_array($enabledCategories)) {
            $enabledCategories = ['novice'];
        }

        $enabledJobs = json_decode((string) SiteSetting::get('char_create_enabled_jobs', ''), true);
        if (!is_array($enabledJobs) || empty($enabledJobs)) {
            $enabledJobs = [0];
        }

        $customJobs = json_decode((string) SiteSetting::get('char_create_custom_jobs', ''), true);
        if (!is_array($customJobs)) {
            $customJobs = [];
        }

        $startingItems = json_decode((string) SiteSetting::get('char_create_starting_items', ''), true);
        if (!is_array($startingItems)) {
            $startingItems = [];
        }

        $maxBase = min($serverMaxBase, max(1, (int) SiteSetting::get('char_create_max_base_level', min(99, $serverMaxBase))));
        $maxJob = min($serverMaxJob, max(1, (int) SiteSetting::get('char_create_max_job_level', min(50, $serverMaxJob))));

        $startBase = max(1, min($maxBase, (int) SiteSetting::get('char_create_starting_base_level', 1)));
        $startJob = max(1, min($maxJob, (int) SiteSetting::get('char_create_starting_job_level', 1)));

        $defaultHairstyle = (int) SiteSetting::get('char_create_default_hairstyle', $hairstyles[0] ?? 1);
        if (!in_array($defaultHairstyle, $hairstyles, true)) {
            $defaultHairstyle = (int) ($hairstyles[0] ?? 1);
        }

        $defaultHairColor = (int) SiteSetting::get('char_create_default_hair_color', $hairColors[0] ?? 0);
        if (!in_array($defaultHairColor, $hairColors, true)) {
            $defaultHairColor = (int) ($hairColors[0] ?? 0);
        }

        return [
            'enabled' => (bool) (int) SiteSetting::get('module_enabled_character_creation', SiteSetting::get('char_create_enabled', '1')),
            'preset' => (string) SiteSetting::get('char_create_preset', 'custom'),
            'chargen_url' => (string) SiteSetting::get(
                'char_create_chargen_url',
                'https://ro-character-simulator.ratemyserver.net/charsim.php?gender={rms_gender}&job={job}&hair={hair}&viewid=0&location=0&direction={dir}&action=0&hdye={color}&dye=0&framenum=0&bg=0'
            ),
            'start_map' => (string) SiteSetting::get('char_create_start_map', 'prontera'),
            'start_x' => max(1, (int) SiteSetting::get('char_create_start_x', 156)),
            'start_y' => max(1, (int) SiteSetting::get('char_create_start_y', 191)),
            'start_zeny' => max(0, (int) SiteSetting::get('char_create_start_zeny', 0)),
            'enable_gender' => (bool) (int) SiteSetting::get('char_create_enable_gender', '1'),
            'hairstyles' => array_values(array_unique(array_map('intval', $hairstyles))),
            'hair_colors' => array_values(array_unique(array_map('intval', $hairColors))),
            'default_hairstyle' => $defaultHairstyle,
            'default_hair_color' => $defaultHairColor,
            'enabled_categories' => array_values(array_unique(array_map('strval', $enabledCategories))),
            'enabled_jobs' => array_values(array_unique(array_map('intval', $enabledJobs))),
            'custom_jobs' => $customJobs,
            'starting_base_level' => $startBase,
            'starting_job_level' => $startJob,
            'max_base_level' => $maxBase,
            'max_job_level' => $maxJob,
            'server_max_base_level' => $serverMaxBase,
            'server_max_job_level' => $serverMaxJob,
            'allow_custom_levels' => (bool) (int) SiteSetting::get('char_create_allow_custom_levels', '0'),
            'max_slots' => max(1, min(15, (int) SiteSetting::get('char_create_max_slots', config('emulator.registration.default_char_slots', 9)))),
            'min_name_length' => max(2, min(12, (int) SiteSetting::get('char_create_min_name_length', 4))),
            'max_name_length' => max(6, min(23, (int) SiteSetting::get('char_create_max_name_length', 23))),
            'allow_spaces_in_name' => (bool) (int) SiteSetting::get('char_create_allow_spaces_in_name', '0'),
            'forbidden_names' => (string) SiteSetting::get('char_create_forbidden_names', 'admin,gm,gamemaster,root,system,aethercp'),
            'require_offline' => (bool) (int) SiteSetting::get('char_create_require_offline', '1'),
            'starting_items' => $startingItems,
        ];
    }

    /**
     * Save settings from Admin Character Creation Settings.
     */
    public function saveSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            SiteSetting::set("char_create_{$key}", is_array($value) ? json_encode($value) : (string) $value, 'character_creation');
            if ($key === 'enabled') {
                SiteSetting::set('module_enabled_character_creation', (string) $value);
            }
        }
    }

    /**
     * Return ONLY the jobs currently allowed by administrator configuration.
     * Never hardcodes a fixed job list for players.
     */
    public function getAvailableJobs(?array $settings = null): array
    {
        $settings ??= $this->getSettings();
        $catalog = $this->getJobCatalog();
        $enabledJobIds = $settings['enabled_jobs'] ?? [];
        $enabledCategories = $settings['enabled_categories'] ?? [];

        $available = [];

        foreach ($catalog as $catKey => $cat) {
            if (!in_array($catKey, $enabledCategories, true)) {
                continue;
            }
            foreach ($cat['jobs'] as $jobId => $jobName) {
                if (in_array((int) $jobId, $enabledJobIds, true)) {
                    $available[(int) $jobId] = [
                        'id' => (int) $jobId,
                        'name' => $jobName,
                        'category' => $catKey,
                        'category_label' => $cat['label'],
                        'sprite_url' => JobHelper::getSpriteUrl((int) $jobId) ?: "https://static.divine-pride.net/images/skilltree/jobs/{$jobId}.png",
                    ];
                }
            }
        }

        // Include admin-configured custom jobs if 'custom' category is enabled or explicitly in enabled_jobs
        if (in_array('custom', $enabledCategories, true)) {
            foreach ($settings['custom_jobs'] ?? [] as $cj) {
                $cid = (int) ($cj['id'] ?? -1);
                $cname = trim((string) ($cj['name'] ?? ''));
                if ($cid >= 0 && $cname !== '' && in_array($cid, $enabledJobIds, true)) {
                    $available[$cid] = [
                        'id' => $cid,
                        'name' => $cname,
                        'category' => 'custom',
                        'category_label' => 'Custom Jobs',
                        'sprite_url' => JobHelper::getSpriteUrl($cid) ?: "https://static.divine-pride.net/images/skilltree/jobs/{$cid}.png",
                    ];
                }
            }
        }

        return array_values($available);
    }

    /**
     * Create a character directly in rAthena database with strict server-side validation.
     */
    public function createCharacter(User $user, array $input): array
    {
        $settings = $this->getSettings();

        if (!$settings['enabled']) {
            return ['success' => false, 'message' => 'Online Character Creation is currently disabled by the server administrator.'];
        }

        if (empty($user->account_id)) {
            return ['success' => false, 'message' => 'Your web account is not linked to an in-game rAthena account.'];
        }

        $accountId = (int) $user->account_id;
        $ragnarok = DB::connection('ragnarok');
        $loginTable = $this->emulator->getTableName('login');
        $charTable = $this->emulator->getTableName('char');
        $invTable = $this->emulator->getTableName('inventory');

        // 1. Validate account exists
        $account = $ragnarok->table($loginTable)->where('account_id', $accountId)->first();
        if (!$account) {
            return ['success' => false, 'message' => 'Linked game account was not found in rAthena database.'];
        }

        // 2. Validate character name strictly
        $name = trim((string) ($input['name'] ?? ''));
        $minLen = $settings['min_name_length'];
        $maxLen = $settings['max_name_length'];
        if (mb_strlen($name) < $minLen || mb_strlen($name) > $maxLen) {
            return ['success' => false, 'message' => "Character name must be between {$minLen} and {$maxLen} characters."];
        }

        $pattern = $settings['allow_spaces_in_name'] ? '/^[a-zA-Z0-9_ ]+$/' : '/^[a-zA-Z0-9_]+$/';
        if (!preg_match($pattern, $name)) {
            return ['success' => false, 'message' => 'Character name contains invalid characters. Only letters, numbers, and underscores are allowed.'];
        }

        $forbidden = array_filter(array_map(fn ($v) => strtolower(trim($v)), explode(',', $settings['forbidden_names'])));
        foreach ($forbidden as $bad) {
            if ($bad !== '' && str_contains(strtolower($name), $bad)) {
                return ['success' => false, 'message' => 'This character name is reserved or not allowed.'];
            }
        }

        // 3. Validate job against dynamically configured available jobs
        $availableJobs = collect($this->getAvailableJobs($settings))->keyBy('id');
        $requestedJobId = (int) ($input['job_id'] ?? -1);
        if (!$availableJobs->has($requestedJobId)) {
            ActivityLog::log('character.create_blocked', "Blocked unauthorized job ID [{$requestedJobId}] during character creation.", $accountId);
            return ['success' => false, 'message' => 'Selected starting job is not permitted by server configuration.'];
        }

        // 4. Validate hairstyle & hair color
        $hairStyle = (int) ($input['hair_style'] ?? $settings['default_hairstyle']);
        if (!in_array($hairStyle, $settings['hairstyles'], true)) {
            return ['success' => false, 'message' => 'Selected hairstyle is not permitted.'];
        }

        $hairColor = (int) ($input['hair_color'] ?? $settings['default_hair_color']);
        if (!in_array($hairColor, $settings['hair_colors'], true)) {
            return ['success' => false, 'message' => 'Selected hair color is not permitted.'];
        }

        // 5. Validate levels (never trust browser values when custom levels are disabled)
        if ($settings['allow_custom_levels']) {
            $baseLevel = (int) ($input['base_level'] ?? $settings['starting_base_level']);
            $jobLevel = (int) ($input['job_level'] ?? $settings['starting_job_level']);

            if ($baseLevel < 1 || $baseLevel > $settings['max_base_level']) {
                return ['success' => false, 'message' => "Base level must be between 1 and {$settings['max_base_level']}."];
            }
            if ($jobLevel < 1 || $jobLevel > $settings['max_job_level']) {
                return ['success' => false, 'message' => "Job level must be between 1 and {$settings['max_job_level']}."];
            }
        } else {
            $baseLevel = $settings['starting_base_level'];
            $jobLevel = $settings['starting_job_level'];
        }

        // 6. Gender handling (rAthena stores gender on login.sex)
        $accountSex = strtoupper((string) ($account->sex ?? 'M'));
        if (!in_array($accountSex, ['M', 'F'], true)) {
            $accountSex = 'M';
        }
        $requestedGender = strtoupper((string) ($input['gender'] ?? $accountSex));
        if (!in_array($requestedGender, ['M', 'F'], true)) {
            $requestedGender = $accountSex;
        }

        try {
            $result = $ragnarok->transaction(function () use (
                $ragnarok, $loginTable, $charTable, $invTable,
                $account, $accountId, $name, $requestedJobId, $hairStyle, $hairColor,
                $baseLevel, $jobLevel, $accountSex, $requestedGender, $settings
            ) {
                // Check online status if required
                $existingChars = $ragnarok->table($charTable)
                    ->where('account_id', $accountId)
                    ->get();

                if ($settings['require_offline'] && $existingChars->contains(fn ($c) => (int) ($c->online ?? 0) > 0)) {
                    return ['success' => false, 'message' => 'Please log out of the game before creating a new character.'];
                }

                // Enforce character slot limits (respect both account character_slots and admin max_slots)
                $accountMaxSlots = max(1, (int) ($account->character_slots ?? $settings['max_slots']));
                $effectiveMaxSlots = min($accountMaxSlots, $settings['max_slots']);

                if ($existingChars->count() >= $effectiveMaxSlots) {
                    return ['success' => false, 'message' => "Character slot limit reached ({$existingChars->count()}/{$effectiveMaxSlots})."];
                }

                // Check duplicate character name (case-insensitive)
                $duplicate = $ragnarok->table($charTable)
                    ->whereRaw('LOWER(name) = LOWER(?)', [$name])
                    ->exists();
                if ($duplicate) {
                    return ['success' => false, 'message' => "Character name \"{$name}\" is already taken."];
                }

                // Find lowest available char_num slot
                $usedSlots = $existingChars->pluck('char_num')->map(fn ($s) => (int) $s)->all();
                $freeSlot = 0;
                while (in_array($freeSlot, $usedSlots, true)) {
                    $freeSlot++;
                }

                // Apply gender: update login.sex when gender selection is enabled
                $finalSex = $accountSex;
                if ($settings['enable_gender'] && $requestedGender !== $accountSex) {
                    $ragnarok->table($loginTable)
                        ->where('account_id', $accountId)
                        ->update(['sex' => $requestedGender]);
                    $finalSex = $requestedGender;
                }

                // Build schema-aware insert payload compatible with rAthena versions
                $columns = Schema::connection('ragnarok')->getColumnListing($charTable);
                $basePayload = [
                    'account_id' => $accountId,
                    'char_num' => $freeSlot,
                    'name' => $name,
                    'class' => $requestedJobId,
                    'base_level' => $baseLevel,
                    'job_level' => $jobLevel,
                    'base_exp' => 0,
                    'job_exp' => 0,
                    'zeny' => $settings['start_zeny'],
                    'str' => 1,
                    'agi' => 1,
                    'vit' => 1,
                    'int' => 1,
                    'dex' => 1,
                    'luk' => 1,
                    'max_hp' => 40,
                    'hp' => 40,
                    'max_sp' => 11,
                    'sp' => 11,
                    'status_point' => 48,
                    'skill_point' => 0,
                    'option' => 0,
                    'karma' => 0,
                    'manner' => 0,
                    'party_id' => 0,
                    'guild_id' => 0,
                    'pet_id' => 0,
                    'homun_id' => 0,
                    'elemental_id' => 0,
                    'hair' => $hairStyle,
                    'hair_color' => $hairColor,
                    'clothes_color' => 0,
                    'body' => 0,
                    'weapon' => 0,
                    'shield' => 0,
                    'head_top' => 0,
                    'head_mid' => 0,
                    'head_bottom' => 0,
                    'robe' => 0,
                    'last_map' => $settings['start_map'],
                    'last_x' => $settings['start_x'],
                    'last_y' => $settings['start_y'],
                    'save_map' => $settings['start_map'],
                    'save_x' => $settings['start_x'],
                    'save_y' => $settings['start_y'],
                    'partner_id' => 0,
                    'online' => 0,
                    'father' => 0,
                    'mother' => 0,
                    'child' => 0,
                    'fame' => 0,
                    'rename' => 0,
                    'delete_date' => 0,
                    'moves' => 0,
                    'unban_time' => 0,
                    'font' => 0,
                    'uniqueitem_counter' => 0,
                    'sex' => $finalSex,
                    'hotkey_rowshift' => 0,
                    'hotkey_rowshift2' => 0,
                    'clan_id' => 0,
                    'title_id' => 0,
                    'show_equip' => 0,
                ];

                $insertData = empty($columns)
                    ? $basePayload
                    : array_intersect_key($basePayload, array_flip($columns));

                $charId = (int) $ragnarok->table($charTable)->insertGetId($insertData);

                // Insert starting equipment & consumables (global + job-specific)
                $itemsToGive = array_merge(
                    $settings['starting_items']['global'] ?? [],
                    $settings['starting_items']['jobs'][(string) $requestedJobId] ?? []
                );

                if (!empty($itemsToGive) && Schema::connection('ragnarok')->hasTable($invTable)) {
                    $invColumns = Schema::connection('ragnarok')->getColumnListing($invTable);
                    foreach ($itemsToGive as $item) {
                        $itemId = (int) ($item['item_id'] ?? 0);
                        $amount = max(1, (int) ($item['amount'] ?? 1));
                        if ($itemId <= 0) {
                            continue;
                        }

                        $invRow = [
                            'char_id' => $charId,
                            'nameid' => $itemId,
                            'amount' => $amount,
                            'equip' => 0,
                            'identify' => 1,
                            'refine' => 0,
                            'attribute' => 0,
                            'card0' => 0,
                            'card1' => 0,
                            'card2' => 0,
                            'card3' => 0,
                            'expire_time' => 0,
                            'favorite' => 0,
                            'bound' => 0,
                        ];

                        $ragnarok->table($invTable)->insert(
                            empty($invColumns) ? $invRow : array_intersect_key($invRow, array_flip($invColumns))
                        );
                    }
                }

                return [
                    'success' => true,
                    'char_id' => $charId,
                    'name' => $name,
                    'sex' => $finalSex,
                    'message' => "Character \"{$name}\" has been created and is ready in-game!",
                ];
            });

            if ($result['success']) {
                ActivityLog::log(
                    'character.create',
                    "Created character [{$name}] (Job #{$requestedJobId}, Lv.{$baseLevel}/{$jobLevel}) online.",
                    $accountId
                );
            } else {
                ActivityLog::log(
                    'character.create_failed',
                    "Failed to create character [{$name}]: {$result['message']}",
                    $accountId
                );
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('Online character creation failed: ' . $e->getMessage(), [
                'account_id' => $accountId,
                'name' => $name,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to create character due to a database error. Please contact support.',
            ];
        }
    }
}
