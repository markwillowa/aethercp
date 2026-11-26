<?php

namespace App\Services\Character;

use App\Contracts\EmulatorInterface;
use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Models\ActivityLog;
use App\Models\CharacterAdminNote;
use App\Models\CharacterDeletionRequest;
use App\Models\CharacterLock;
use App\Models\CharacterServiceLog;
use App\Models\CharacterServiceTransaction;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\CashPoints\CashPointsService;
use App\Services\Emulator\JobHelper;
use App\Services\Escrow\EscrowInboxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class CharacterManagementService
{
    /** @var array<string, array<int, string>> */
    private array $columnCache = [];

    public function __construct(
        private readonly EmulatorInterface $emulator,
        private readonly CashPointsService $cashPointsService,
        private readonly EscrowInboxService $escrowInboxService,
        private readonly ItemRepositoryInterface $itemRepository,
    ) {}

    public function getSettings(): array
    {
        return [
            'rename_enabled' => filter_var(SiteSetting::get('char_svc_rename_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'rename_cost' => (int) SiteSetting::get('char_svc_rename_cost', 500),
            'rename_cooldown_hours' => (int) SiteSetting::get('char_svc_rename_cooldown_hours', 168),
            'rename_min_level' => (int) SiteSetting::get('char_svc_rename_min_level', 1),
            'rename_max_level' => (int) SiteSetting::get('char_svc_rename_max_level', 999),
            'rename_blocked_words' => (string) SiteSetting::get('char_svc_rename_blocked_words', 'admin,gm,moderator,system,aether'),

            'appearance_enabled' => filter_var(SiteSetting::get('char_svc_appearance_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'appearance_cost' => (int) SiteSetting::get('char_svc_appearance_cost', 250),
            'appearance_cooldown_hours' => (int) SiteSetting::get('char_svc_appearance_cooldown_hours', 24),
            'appearance_max_hair' => (int) SiteSetting::get('char_svc_appearance_max_hair', 30),
            'appearance_max_hair_color' => (int) SiteSetting::get('char_svc_appearance_max_hair_color', 10),
            'appearance_max_clothes_color' => (int) SiteSetting::get('char_svc_appearance_max_clothes_color', 7),

            'stat_reset_enabled' => filter_var(SiteSetting::get('char_svc_stat_reset_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'stat_reset_cost' => (int) SiteSetting::get('char_svc_stat_reset_cost', 300),
            'stat_reset_cooldown_hours' => (int) SiteSetting::get('char_svc_stat_reset_cooldown_hours', 72),
            'stat_reset_min_level' => (int) SiteSetting::get('char_svc_stat_reset_min_level', 10),

            'skill_reset_enabled' => filter_var(SiteSetting::get('char_svc_skill_reset_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'skill_reset_cost' => (int) SiteSetting::get('char_svc_skill_reset_cost', 300),
            'skill_reset_cooldown_hours' => (int) SiteSetting::get('char_svc_skill_reset_cooldown_hours', 72),
            'skill_reset_min_level' => (int) SiteSetting::get('char_svc_skill_reset_min_level', 10),

            'gender_enabled' => filter_var(SiteSetting::get('char_svc_gender_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'gender_cost' => (int) SiteSetting::get('char_svc_gender_cost', 1000),
            'gender_cooldown_hours' => (int) SiteSetting::get('char_svc_gender_cooldown_hours', 720),

            'job_change_enabled' => filter_var(SiteSetting::get('char_svc_job_change_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'job_change_cost' => (int) SiteSetting::get('char_svc_job_change_cost', 1000),
            'job_change_cooldown_hours' => (int) SiteSetting::get('char_svc_job_change_cooldown_hours', 168),
            'job_change_min_level' => (int) SiteSetting::get('char_svc_job_change_min_level', 1),
            'job_change_reset_skills' => filter_var(SiteSetting::get('char_svc_job_change_reset_skills', '1'), FILTER_VALIDATE_BOOLEAN),
            'job_change_allowed_jobs' => (string) SiteSetting::get('char_svc_job_change_allowed_jobs', ''),

            'deletion_enabled' => filter_var(SiteSetting::get('char_svc_deletion_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'deletion_wait_hours' => (int) SiteSetting::get('char_svc_deletion_wait_hours', 24),
            'deletion_allow_recovery' => filter_var(SiteSetting::get('char_svc_deletion_allow_recovery', '1'), FILTER_VALIDATE_BOOLEAN),

            'lock_enabled' => filter_var(SiteSetting::get('char_svc_lock_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
        ];
    }

    public function updateSettings(array $data, User $admin): void
    {
        $booleanKeys = [
            'rename_enabled', 'appearance_enabled', 'stat_reset_enabled',
            'skill_reset_enabled', 'gender_enabled', 'job_change_enabled',
            'job_change_reset_skills', 'deletion_enabled',
            'deletion_allow_recovery', 'lock_enabled',
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $booleanKeys, true)) {
                SiteSetting::set('char_svc_' . $key, $value ? '1' : '0', 'character_management');
            } else {
                SiteSetting::set('char_svc_' . $key, (string) $value, 'character_management');
            }
        }

        ActivityLog::log('char_mgmt.settings_updated', 'Updated Character Management service settings.', null, ['admin_id' => $admin->id]);
    }

    public function tableColumns(string $tableKey): array
    {
        $table = $this->emulator->getTableName($tableKey);
        if (!isset($this->columnCache[$table])) {
            $this->columnCache[$table] = Schema::connection('ragnarok')->getColumnListing($table);
        }
        return $this->columnCache[$table];
    }

    public function hasColumn(string $tableKey, string $column): bool
    {
        return in_array($column, $this->tableColumns($tableKey), true);
    }

    public function getCharactersForAccount(int $accountId): array
    {
        $charTable = $this->emulator->getTableName('char');
        $guildTable = $this->emulator->getTableName('guild');

        $chars = DB::connection('ragnarok')
            ->table($charTable . ' as c')
            ->leftJoin($guildTable . ' as g', 'c.guild_id', '=', 'g.guild_id')
            ->where('c.account_id', $accountId)
            ->orderBy('c.char_num')
            ->select(['c.*', 'g.name as guild_name'])
            ->get()
            ->all();

        $charIds = array_map(fn ($c) => (int) $c->char_id, $chars);
        $locks = CharacterLock::whereIn('char_id', $charIds)->where('is_locked', true)->get()->keyBy('char_id');
        $deletions = CharacterDeletionRequest::whereIn('char_id', $charIds)
            ->whereIn('status', ['pending', 'executed'])
            ->orderByDesc('id')
            ->get()
            ->unique('char_id')
            ->keyBy('char_id');

        foreach ($chars as $char) {
            $char->job_name = JobHelper::getName((int) $char->class);
            $char->active_lock = $locks->get($char->char_id);
            $char->deletion_request = $deletions->get($char->char_id);
        }

        return $chars;
    }

    public function getOwnedCharacterOrFail(User $user, int $charId): object
    {
        $accountId = (int) ($user->account_id ?? 0);
        if ($accountId <= 0) {
            throw new RuntimeException('Your web account is not linked to a game account.');
        }

        $charTable = $this->emulator->getTableName('char');
        $guildTable = $this->emulator->getTableName('guild');

        $char = DB::connection('ragnarok')
            ->table($charTable . ' as c')
            ->leftJoin($guildTable . ' as g', 'c.guild_id', '=', 'g.guild_id')
            ->where('c.char_id', $charId)
            ->where('c.account_id', $accountId)
            ->select(['c.*', 'g.name as guild_name'])
            ->first();

        if (!$char) {
            throw new RuntimeException('Character not found or does not belong to your account.');
        }

        $char->job_name = JobHelper::getName((int) $char->class);

        return $char;
    }

    public function getCharacterDetailForAdmin(int $charId): array
    {
        $charTable = $this->emulator->getTableName('char');
        $loginTable = $this->emulator->getTableName('login');
        $guildTable = $this->emulator->getTableName('guild');
        $partyTable = $this->emulator->getTableName('party');

        $char = DB::connection('ragnarok')
            ->table($charTable . ' as c')
            ->leftJoin($loginTable . ' as l', 'c.account_id', '=', 'l.account_id')
            ->leftJoin($guildTable . ' as g', 'c.guild_id', '=', 'g.guild_id')
            ->leftJoin($partyTable . ' as p', 'c.party_id', '=', 'p.party_id')
            ->where('c.char_id', $charId)
            ->select([
                'c.*',
                'l.userid as account_userid',
                'l.sex as account_sex',
                'l.email as account_email',
                'l.group_id as account_group_id',
                'l.state as account_state',
                'g.name as guild_name',
                'p.name as party_name',
            ])
            ->first();

        if (!$char) {
            throw new RuntimeException("Character #{$charId} not found.");
        }

        $char->job_name = JobHelper::getName((int) $char->class);
        $accountId = (int) $char->account_id;

        $inventory = $this->enrichItems($this->getRawItems('inventory', 'char_id', $charId));
        $equipped = array_values(array_filter($inventory, fn ($item) => (int) ($item->equip ?? 0) > 0));
        $cart = $this->enrichItems($this->getRawItems('cart_inventory', 'char_id', $charId));
        $storage = $this->enrichItems($this->getRawItems('storage', 'account_id', $accountId));

        $skills = DB::connection('ragnarok')
            ->table($this->emulator->getTableName('skill'))
            ->where('char_id', $charId)
            ->orderBy('id')
            ->get()
            ->all();

        return [
            'character' => $char,
            'cash_points' => $this->cashPointsService->getBalance($accountId),
            'inventory' => $inventory,
            'equipped' => $equipped,
            'cart' => $cart,
            'storage' => $storage,
            'skills' => $skills,
            'active_lock' => CharacterLock::where('char_id', $charId)->where('is_locked', true)->latest('id')->first(),
            'deletion_requests' => CharacterDeletionRequest::where('char_id', $charId)->latest('id')->get(),
            'transactions' => CharacterServiceTransaction::where('char_id', $charId)->latest('id')->limit(30)->get(),
            'logs' => CharacterServiceLog::where('char_id', $charId)->latest('id')->limit(40)->get(),
            'notes' => CharacterAdminNote::with('admin')->where('char_id', $charId)->latest('id')->get(),
            'supported_columns' => $this->tableColumns('char'),
        ];
    }

    public function getCharacterDetailForPlayer(User $user, int $charId): array
    {
        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $accountId = (int) $char->account_id;

        $login = DB::connection('ragnarok')
            ->table($this->emulator->getTableName('login'))
            ->where('account_id', $accountId)
            ->first();

        $inventory = $this->enrichItems($this->getRawItems('inventory', 'char_id', $charId));
        $equipped = array_values(array_filter($inventory, fn ($item) => (int) ($item->equip ?? 0) > 0));
        $bagInventory = array_values(array_filter($inventory, fn ($item) => (int) ($item->equip ?? 0) === 0));
        $storage = $this->enrichItems($this->getRawItems('storage', 'account_id', $accountId));

        return [
            'character' => $char,
            'account' => $login,
            'cash_points' => $this->cashPointsService->getBalance($accountId),
            'equipped' => $equipped,
            'inventory' => $bagInventory,
            'storage' => $storage,
            'active_lock' => CharacterLock::where('char_id', $charId)->where('is_locked', true)->latest('id')->first(),
            'deletion_request' => CharacterDeletionRequest::where('char_id', $charId)->latest('id')->first(),
            'transactions' => CharacterServiceTransaction::where('char_id', $charId)->latest('id')->limit(15)->get(),
            'logs' => CharacterServiceLog::where('char_id', $charId)->latest('id')->limit(20)->get(),
            'settings' => $this->getSettings(),
            'cooldowns' => $this->getCooldownStatuses($charId),
            'privacy' => \App\Models\PlayerProfileSetting::forCharacter($charId, $accountId),
            'hairColors' => app(CharacterCreationService::class)->getHairColorCatalog(),
            'availableJobs' => $this->getAllowedJobsForChange($this->getSettings()),
            'supported_columns' => $this->tableColumns('char'),
        ];
    }

    public function getAdminCharacters(array $filters = [], int $perPage = 25)
    {
        $charTable = $this->emulator->getTableName('char');
        $loginTable = $this->emulator->getTableName('login');
        $guildTable = $this->emulator->getTableName('guild');

        $query = DB::connection('ragnarok')
            ->table($charTable . ' as c')
            ->leftJoin($loginTable . ' as l', 'c.account_id', '=', 'l.account_id')
            ->leftJoin($guildTable . ' as g', 'c.guild_id', '=', 'g.guild_id')
            ->select([
                'c.*',
                'l.userid as account_userid',
                'g.name as guild_name',
            ]);

        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $query->where(function ($sub) use ($q) {
                $sub->where('c.name', 'like', "%{$q}%")
                    ->orWhere('l.userid', 'like', "%{$q}%")
                    ->orWhere('g.name', 'like', "%{$q}%");
                if (is_numeric($q)) {
                    $sub->orWhere('c.char_id', (int) $q)
                        ->orWhere('c.account_id', (int) $q);
                }
            });
        }

        if (isset($filters['online']) && $filters['online'] !== '') {
            if ($filters['online'] === '1') {
                $query->where('c.online', '>', 0);
            } elseif ($filters['online'] === '0') {
                $query->where('c.online', 0);
            }
        }

        if (!empty($filters['class']) && is_numeric($filters['class'])) {
            $query->where('c.class', (int) $filters['class']);
        }

        if (!empty($filters['min_level']) && is_numeric($filters['min_level'])) {
            $query->where('c.base_level', '>=', (int) $filters['min_level']);
        }

        if (!empty($filters['max_level']) && is_numeric($filters['max_level'])) {
            $query->where('c.base_level', '<=', (int) $filters['max_level']);
        }

        if (!empty($filters['locked']) && $filters['locked'] === '1') {
            $lockedIds = CharacterLock::where('is_locked', true)->pluck('char_id')->all();
            $query->whereIn('c.char_id', !empty($lockedIds) ? $lockedIds : [0]);
        }

        $paginator = $query->orderByDesc('c.base_level')->orderBy('c.char_id')->paginate($perPage)->withQueryString();

        $charIds = collect($paginator->items())->pluck('char_id')->all();
        $locks = CharacterLock::whereIn('char_id', $charIds)->where('is_locked', true)->get()->keyBy('char_id');

        foreach ($paginator->items() as $item) {
            $item->job_name = JobHelper::getName((int) $item->class);
            $item->active_lock = $locks->get($item->char_id);
        }

        return $paginator;
    }

    public function getCooldownStatuses(int $charId): array
    {
        $settings = $this->getSettings();
        $services = [
            'rename' => $settings['rename_cooldown_hours'],
            'appearance' => $settings['appearance_cooldown_hours'],
            'stat_reset' => $settings['stat_reset_cooldown_hours'],
            'skill_reset' => $settings['skill_reset_cooldown_hours'],
            'gender_change' => $settings['gender_cooldown_hours'],
            'job_change' => $settings['job_change_cooldown_hours'],
        ];

        $statuses = [];
        foreach ($services as $type => $hours) {
            if ($hours <= 0) {
                $statuses[$type] = ['on_cooldown' => false, 'available_at' => null];
                continue;
            }
            $lastTx = CharacterServiceTransaction::where('char_id', $charId)
                ->where('service_type', $type)
                ->where('status', 'completed')
                ->latest('created_at')
                ->first();

            if ($lastTx && $lastTx->created_at->copy()->addHours($hours)->isFuture()) {
                $statuses[$type] = [
                    'on_cooldown' => true,
                    'available_at' => $lastTx->created_at->copy()->addHours($hours),
                ];
            } else {
                $statuses[$type] = ['on_cooldown' => false, 'available_at' => null];
            }
        }

        return $statuses;
    }

    public function renameCharacter(User $user, int $charId, string $newName, string $idempotencyKey, ?string $ip = null): CharacterServiceTransaction
    {
        if ($existing = CharacterServiceTransaction::where('idempotency_key', $idempotencyKey)->first()) {
            return $existing;
        }

        $settings = $this->getSettings();
        if (!$settings['rename_enabled']) {
            throw new RuntimeException('Character Rename service is currently disabled.');
        }

        $newName = trim($newName);
        $this->validateCharacterName($newName, $settings['rename_blocked_words']);

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $this->guardPlayerService($char, 'rename', $settings['rename_cooldown_hours'], (int) $settings['rename_min_level'], (int) $settings['rename_max_level']);

        if (strcasecmp((string) $char->name, $newName) === 0) {
            throw new RuntimeException('The new name must be different from your current character name.');
        }

        $charTable = $this->emulator->getTableName('char');
        $exists = DB::connection('ragnarok')->table($charTable)
            ->whereRaw('LOWER(name) = ?', [strtolower($newName)])
            ->where('char_id', '!=', $charId)
            ->exists();
        if ($exists) {
            throw new RuntimeException('That character name is already taken.');
        }

        return $this->executePaidService(
            $user,
            $char,
            'rename',
            (int) $settings['rename_cost'],
            $idempotencyKey,
            ['name' => $char->name],
            ['name' => $newName],
            function () use ($charTable, $charId, $newName) {
                DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update(['name' => $newName]);
            },
            $ip
        );
    }

    public function changeAppearance(User $user, int $charId, array $input, string $idempotencyKey, ?string $ip = null): CharacterServiceTransaction
    {
        if ($existing = CharacterServiceTransaction::where('idempotency_key', $idempotencyKey)->first()) {
            return $existing;
        }

        $settings = $this->getSettings();
        if (!$settings['appearance_enabled']) {
            throw new RuntimeException('Appearance Change service is currently disabled.');
        }

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $this->guardPlayerService($char, 'appearance', $settings['appearance_cooldown_hours']);

        $updates = [];
        $oldValues = [];
        $allowedMap = [
            'hair' => [0, max(1, (int) $settings['appearance_max_hair'])],
            'hair_color' => [0, max(1, (int) $settings['appearance_max_hair_color'])],
            'clothes_color' => [0, max(1, (int) $settings['appearance_max_clothes_color'])],
        ];

        foreach ($allowedMap as $col => [$min, $max]) {
            if ($this->hasColumn('char', $col) && array_key_exists($col, $input) && $input[$col] !== null && $input[$col] !== '') {
                $val = (int) $input[$col];
                if ($val < $min || $val > $max) {
                    throw new RuntimeException("Invalid {$col} value. Allowed range is {$min} to {$max}.");
                }
                $updates[$col] = $val;
                $oldValues[$col] = (int) ($char->{$col} ?? 0);
            }
        }

        if (empty($updates)) {
            throw new RuntimeException('No valid appearance changes were provided.');
        }

        $charTable = $this->emulator->getTableName('char');

        return $this->executePaidService(
            $user,
            $char,
            'appearance',
            (int) $settings['appearance_cost'],
            $idempotencyKey,
            $oldValues,
            $updates,
            function () use ($charTable, $charId, $updates) {
                DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update($updates);
            },
            $ip
        );
    }

    public function resetStats(User $user, int $charId, string $idempotencyKey, ?string $ip = null): CharacterServiceTransaction
    {
        if ($existing = CharacterServiceTransaction::where('idempotency_key', $idempotencyKey)->first()) {
            return $existing;
        }

        $settings = $this->getSettings();
        if (!$settings['stat_reset_enabled']) {
            throw new RuntimeException('Stat Reset service is currently disabled.');
        }

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $this->guardPlayerService($char, 'stat_reset', $settings['stat_reset_cooldown_hours'], (int) $settings['stat_reset_min_level']);

        [$oldValues, $newValues, $updatePayload] = $this->computeStatResetPayload($char);
        $charTable = $this->emulator->getTableName('char');

        return $this->executePaidService(
            $user,
            $char,
            'stat_reset',
            (int) $settings['stat_reset_cost'],
            $idempotencyKey,
            $oldValues,
            $newValues,
            function () use ($charTable, $charId, $updatePayload) {
                DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update($updatePayload);
            },
            $ip
        );
    }

    public function resetSkills(User $user, int $charId, string $idempotencyKey, ?string $ip = null): CharacterServiceTransaction
    {
        if ($existing = CharacterServiceTransaction::where('idempotency_key', $idempotencyKey)->first()) {
            return $existing;
        }

        $settings = $this->getSettings();
        if (!$settings['skill_reset_enabled']) {
            throw new RuntimeException('Skill Reset service is currently disabled.');
        }

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $this->guardPlayerService($char, 'skill_reset', $settings['skill_reset_cooldown_hours'], (int) $settings['skill_reset_min_level']);

        $skillTable = $this->emulator->getTableName('skill');
        $charTable = $this->emulator->getTableName('char');

        $learnedPoints = (int) DB::connection('ragnarok')
            ->table($skillTable)
            ->where('char_id', $charId)
            ->where('flag', 0)
            ->where('lv', '>', 0)
            ->sum('lv');

        if ($learnedPoints <= 0) {
            throw new RuntimeException('This character has no allocated skill points to reset.');
        }

        $oldSkillPoint = (int) $char->skill_point;
        $newSkillPoint = $oldSkillPoint + $learnedPoints;

        return $this->executePaidService(
            $user,
            $char,
            'skill_reset',
            (int) $settings['skill_reset_cost'],
            $idempotencyKey,
            ['skill_point' => $oldSkillPoint, 'allocated_skills' => $learnedPoints],
            ['skill_point' => $newSkillPoint, 'allocated_skills' => 0],
            function () use ($skillTable, $charTable, $charId, $newSkillPoint) {
                DB::connection('ragnarok')->table($skillTable)
                    ->where('char_id', $charId)
                    ->where('flag', 0)
                    ->delete();
                DB::connection('ragnarok')->table($charTable)
                    ->where('char_id', $charId)
                    ->update(['skill_point' => $newSkillPoint]);
            },
            $ip
        );
    }

    public function changeAccountGender(User $user, int $charId, string $targetGender, string $idempotencyKey, ?string $ip = null): CharacterServiceTransaction
    {
        if ($existing = CharacterServiceTransaction::where('idempotency_key', $idempotencyKey)->first()) {
            return $existing;
        }

        $settings = $this->getSettings();
        if (!$settings['gender_enabled']) {
            throw new RuntimeException('Gender Change service is currently disabled.');
        }

        $targetGender = strtoupper(trim($targetGender));
        if (!in_array($targetGender, ['M', 'F'], true)) {
            throw new RuntimeException('Gender must be M (Male) or F (Female).');
        }

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $this->guardPlayerService($char, 'gender_change', $settings['gender_cooldown_hours']);

        $accountId = (int) $char->account_id;
        $charTable = $this->emulator->getTableName('char');
        $loginTable = $this->emulator->getTableName('login');

        // rAthena stores gender at the account level; ALL characters on this account must be offline.
        $onlineCount = DB::connection('ragnarok')->table($charTable)
            ->where('account_id', $accountId)
            ->where('online', '>', 0)
            ->count();
        if ($onlineCount > 0) {
            throw new RuntimeException('All characters on your game account must be offline before changing account gender.');
        }

        $login = DB::connection('ragnarok')->table($loginTable)->where('account_id', $accountId)->first();
        if (!$login) {
            throw new RuntimeException('Linked game account not found.');
        }

        $currentSex = strtoupper((string) $login->sex);
        if ($currentSex === $targetGender) {
            throw new RuntimeException("Your game account gender is already {$targetGender}.");
        }

        return $this->executePaidService(
            $user,
            $char,
            'gender_change',
            (int) $settings['gender_cost'],
            $idempotencyKey,
            ['account_sex' => $currentSex],
            ['account_sex' => $targetGender],
            function () use ($loginTable, $charTable, $accountId, $targetGender) {
                DB::connection('ragnarok')->table($loginTable)
                    ->where('account_id', $accountId)
                    ->update(['sex' => $targetGender]);

                if ($this->hasColumn('char', 'sex')) {
                    DB::connection('ragnarok')->table($charTable)
                        ->where('account_id', $accountId)
                        ->update(['sex' => $targetGender]);
                }
            },
            $ip
        );
    }

    public function getAllowedJobsForChange(array $settings): array
    {
        $allJobs = config('jobs', []);
        $allowedRaw = trim((string) ($settings['job_change_allowed_jobs'] ?? ''));
        $allowedIds = $allowedRaw !== ''
            ? array_map('intval', array_filter(array_map('trim', explode(',', $allowedRaw)), 'is_numeric'))
            : [];

        $result = [];
        foreach ($allJobs as $id => $name) {
            if (!empty($allowedIds) && !in_array((int) $id, $allowedIds, true)) {
                continue;
            }
            $result[] = [
                'id' => (int) $id,
                'name' => $name,
                'sprite_url' => JobHelper::getSpriteUrl((int) $id),
            ];
        }

        return $result;
    }

    public function changeJob(User $user, int $charId, int $targetClass, string $idempotencyKey, ?string $ip = null): CharacterServiceTransaction
    {
        if ($existing = CharacterServiceTransaction::where('idempotency_key', $idempotencyKey)->first()) {
            return $existing;
        }

        $settings = $this->getSettings();
        if (!$settings['job_change_enabled']) {
            throw new RuntimeException('Job Change service is currently disabled.');
        }

        $allJobs = config('jobs', []);
        if (!array_key_exists($targetClass, $allJobs)) {
            throw new RuntimeException('Selected job class is not valid.');
        }

        $allowedJobs = collect($this->getAllowedJobsForChange($settings))->pluck('id')->all();
        if (!in_array($targetClass, $allowedJobs, true)) {
            throw new RuntimeException('Selected job class is not allowed for Job Change.');
        }

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $this->guardPlayerService($char, 'job_change', $settings['job_change_cooldown_hours'], (int) $settings['job_change_min_level']);

        if ((int) $char->class === $targetClass) {
            throw new RuntimeException('Character is already that job class.');
        }

        $charTable = $this->emulator->getTableName('char');
        $skillTable = $this->emulator->getTableName('skill');
        $oldClass = (int) $char->class;

        return $this->executePaidService(
            $user,
            $char,
            'job_change',
            (int) $settings['job_change_cost'],
            $idempotencyKey,
            ['class' => $oldClass, 'job_name' => JobHelper::getName($oldClass)],
            ['class' => $targetClass, 'job_name' => JobHelper::getName($targetClass)],
            function () use ($charTable, $skillTable, $charId, $targetClass, $char, $settings) {
                $updates = ['class' => $targetClass];
                if ($settings['job_change_reset_skills']) {
                    $pts = (int) DB::connection('ragnarok')->table($skillTable)
                        ->where('char_id', $charId)
                        ->where('flag', 0)
                        ->where('lv', '>', 0)
                        ->sum('lv');
                    DB::connection('ragnarok')->table($skillTable)
                        ->where('char_id', $charId)
                        ->where('flag', 0)
                        ->delete();
                    $updates['skill_point'] = (int) $char->skill_point + $pts;
                }
                DB::connection('ragnarok')->table($charTable)
                    ->where('char_id', $charId)
                    ->update($updates);
            },
            $ip
        );
    }

    public function togglePlayerLock(User $user, int $charId, bool $lock, string $reason = 'Player self-lock', ?string $ip = null): void
    {
        $settings = $this->getSettings();
        if (!$settings['lock_enabled']) {
            throw new RuntimeException('Character Lock is currently disabled.');
        }

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $activeLock = CharacterLock::where('char_id', $charId)->where('is_locked', true)->latest('id')->first();

        if ($lock) {
            if ($activeLock) {
                throw new RuntimeException('Character is already locked.');
            }
            CharacterLock::create([
                'char_id' => $charId,
                'account_id' => (int) $char->account_id,
                'is_locked' => true,
                'lock_scope' => 'services',
                'reason' => trim($reason) !== '' ? trim($reason) : 'Player self-lock',
                'locked_by' => $user->id,
                'locked_at' => now(),
            ]);
            $this->logAction($user->id, 'player', (int) $char->account_id, $charId, 'character.locked', null, ['scope' => 'services'], 0, null, 'success', null, $ip);
        } else {
            if (!$activeLock) {
                throw new RuntimeException('Character is not currently locked.');
            }
            if ((int) $activeLock->locked_by !== (int) $user->id || $activeLock->lock_scope === 'full') {
                throw new RuntimeException('This character is locked by an administrator and cannot be unlocked by the player.');
            }
            $activeLock->update([
                'is_locked' => false,
                'unlocked_by' => $user->id,
                'unlocked_at' => now(),
            ]);
            $this->logAction($user->id, 'player', (int) $char->account_id, $charId, 'character.unlocked', ['scope' => $activeLock->lock_scope], null, 0, null, 'success', null, $ip);
        }
    }

    public function requestDeletion(User $user, int $charId, ?string $ip = null): CharacterDeletionRequest
    {
        $settings = $this->getSettings();
        if (!$settings['deletion_enabled']) {
            throw new RuntimeException('Character Deletion is currently disabled.');
        }

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        if ((int) $char->online > 0) {
            throw new RuntimeException('Character must be offline before requesting deletion.');
        }

        $activeLock = CharacterLock::where('char_id', $charId)->where('is_locked', true)->first();
        if ($activeLock) {
            throw new RuntimeException('Locked characters cannot be deleted. Unlock the character first.');
        }

        $existing = CharacterDeletionRequest::where('char_id', $charId)->where('status', 'pending')->first();
        if ($existing) {
            throw new RuntimeException('A deletion request is already pending for this character.');
        }

        $waitHours = max(1, (int) $settings['deletion_wait_hours']);
        $scheduled = now()->addHours($waitHours);

        $req = CharacterDeletionRequest::create([
            'user_id' => $user->id,
            'account_id' => (int) $char->account_id,
            'char_id' => $charId,
            'char_name' => $char->name,
            'status' => 'pending',
            'requested_at' => now(),
            'scheduled_delete_at' => $scheduled,
        ]);

        if ($this->hasColumn('char', 'delete_date')) {
            DB::connection('ragnarok')->table($this->emulator->getTableName('char'))
                ->where('char_id', $charId)
                ->update(['delete_date' => $scheduled->timestamp]);
        }

        $this->logAction($user->id, 'player', (int) $char->account_id, $charId, 'deletion.requested', ['delete_date' => $char->delete_date ?? 0], ['scheduled_delete_at' => $scheduled->toDateTimeString()], 0, null, 'success', null, $ip);

        return $req;
    }

    public function cancelDeletion(User $user, int $charId, ?string $ip = null): void
    {
        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $req = CharacterDeletionRequest::where('char_id', $charId)->where('status', 'pending')->latest('id')->first();

        if (!$req) {
            throw new RuntimeException('No pending deletion request found for this character.');
        }

        $req->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        if ($this->hasColumn('char', 'delete_date')) {
            DB::connection('ragnarok')->table($this->emulator->getTableName('char'))
                ->where('char_id', $charId)
                ->update(['delete_date' => 0]);
        }

        $this->logAction($user->id, 'player', (int) $char->account_id, $charId, 'deletion.cancelled', null, ['delete_date' => 0], 0, null, 'success', null, $ip);
    }

    public function requestRecovery(User $user, int $charId, ?string $ip = null): CharacterDeletionRequest
    {
        $settings = $this->getSettings();
        if (!$settings['deletion_allow_recovery']) {
            throw new RuntimeException('Character recovery requests are currently disabled.');
        }

        $char = $this->getOwnedCharacterOrFail($user, $charId);
        $req = CharacterDeletionRequest::where('char_id', $charId)
            ->whereIn('status', ['pending', 'executed'])
            ->latest('id')
            ->first();

        if (!$req) {
            throw new RuntimeException('No eligible deletion record found to recover.');
        }

        $req->update(['recovery_status' => 'pending_approval']);
        $this->logAction($user->id, 'player', (int) $char->account_id, $charId, 'recovery.requested', null, ['request_id' => $req->id], 0, null, 'success', null, $ip);

        return $req;
    }

    public function adminUpdateCharacter(User $admin, int $charId, array $input, ?string $ip = null): void
    {
        $charTable = $this->emulator->getTableName('char');
        $char = DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->first();
        if (!$char) {
            throw new RuntimeException("Character #{$charId} not found.");
        }

        if ((int) $char->online > 0) {
            throw new RuntimeException('Character is currently online. Force logout or wait for the player to log off before modifying character data.');
        }

        $editableFields = [
            'name', 'class', 'base_level', 'job_level', 'base_exp', 'job_exp', 'zeny',
            'str', 'agi', 'vit', 'int', 'dex', 'luk',
            'pow', 'sta', 'wis', 'spl', 'con', 'crt',
            'status_point', 'skill_point', 'trait_point',
            'hair', 'hair_color', 'clothes_color',
            'last_map', 'last_x', 'last_y', 'save_map', 'save_x', 'save_y',
        ];

        $updates = [];
        $previous = [];
        foreach ($editableFields as $field) {
            if (array_key_exists($field, $input) && $input[$field] !== null && $input[$field] !== '' && $this->hasColumn('char', $field)) {
                $val = in_array($field, ['name', 'last_map', 'save_map'], true) ? trim((string) $input[$field]) : (int) $input[$field];
                if ($field === 'name' && strcasecmp($val, (string) $char->name) !== 0) {
                    $dup = DB::connection('ragnarok')->table($charTable)->whereRaw('LOWER(name) = ?', [strtolower($val)])->where('char_id', '!=', $charId)->exists();
                    if ($dup) {
                        throw new RuntimeException("Character name '{$val}' is already in use.");
                    }
                }
                if ((string) ($char->{$field} ?? '') !== (string) $val) {
                    $previous[$field] = $char->{$field} ?? null;
                    $updates[$field] = $val;
                }
            }
        }

        if (empty($updates)) {
            throw new RuntimeException('No changes were detected.');
        }

        DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update($updates);
        $this->logAction($admin->id, 'admin', (int) $char->account_id, $charId, 'admin.edit', $previous, $updates, 0, null, 'success', null, $ip);
        ActivityLog::log('char_mgmt.admin_edit', "Admin edited character #{$charId} ({$char->name}).", (int) $char->account_id, ['updated_fields' => array_keys($updates)]);
    }

    public function adminModerate(User $admin, int $charId, string $action, array $params = [], ?string $ip = null): string
    {
        $charTable = $this->emulator->getTableName('char');
        $char = DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->first();
        if (!$char) {
            throw new RuntimeException("Character #{$charId} not found.");
        }

        $accountId = (int) $char->account_id;

        switch ($action) {
            case 'force_logout':
                DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update(['online' => 0]);
                $this->logAction($admin->id, 'admin', $accountId, $charId, 'admin.force_logout', ['online' => $char->online], ['online' => 0], 0, null, 'success', null, $ip);
                return 'Cleared online status flag for character.';

            case 'lock':
                $reason = trim((string) ($params['reason'] ?? 'Locked by Administrator'));
                $scope = in_array($params['lock_scope'] ?? 'full', ['services', 'full'], true) ? $params['lock_scope'] : 'full';
                CharacterLock::where('char_id', $charId)->where('is_locked', true)->update(['is_locked' => false, 'unlocked_by' => $admin->id, 'unlocked_at' => now()]);
                CharacterLock::create([
                    'char_id' => $charId,
                    'account_id' => $accountId,
                    'is_locked' => true,
                    'lock_scope' => $scope,
                    'reason' => $reason ?: 'Locked by Administrator',
                    'locked_by' => $admin->id,
                    'locked_at' => now(),
                ]);
                $this->logAction($admin->id, 'admin', $accountId, $charId, 'admin.lock', null, ['scope' => $scope, 'reason' => $reason], 0, null, 'success', null, $ip);
                return 'Character locked.';

            case 'unlock':
                CharacterLock::where('char_id', $charId)->where('is_locked', true)->update([
                    'is_locked' => false,
                    'unlocked_by' => $admin->id,
                    'unlocked_at' => now(),
                ]);
                $this->logAction($admin->id, 'admin', $accountId, $charId, 'admin.unlock', null, ['is_locked' => false], 0, null, 'success', null, $ip);
                return 'Character unlocked.';

            case 'move_save_point':
                if ((int) $char->online > 0) {
                    throw new RuntimeException('Character must be offline to move coordinates.');
                }
                DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update([
                    'last_map' => $char->save_map,
                    'last_x' => (int) $char->save_x,
                    'last_y' => (int) $char->save_y,
                ]);
                $this->logAction($admin->id, 'admin', $accountId, $charId, 'admin.move_save_point', ['last_map' => $char->last_map], ['last_map' => $char->save_map], 0, null, 'success', null, $ip);
                return "Moved character to save point ({$char->save_map}).";

            case 'reset_stats':
                if ((int) $char->online > 0) {
                    throw new RuntimeException('Character must be offline to reset stats.');
                }
                [$oldValues, $newValues, $payload] = $this->computeStatResetPayload($char);
                DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update($payload);
                $this->logAction($admin->id, 'admin', $accountId, $charId, 'admin.reset_stats', $oldValues, $newValues, 0, null, 'success', null, $ip);
                return 'Character stats reset successfully.';

            case 'reset_skills':
                if ((int) $char->online > 0) {
                    throw new RuntimeException('Character must be offline to reset skills.');
                }
                $skillTable = $this->emulator->getTableName('skill');
                $pts = (int) DB::connection('ragnarok')->table($skillTable)->where('char_id', $charId)->where('flag', 0)->sum('lv');
                DB::connection('ragnarok')->table($skillTable)->where('char_id', $charId)->where('flag', 0)->delete();
                DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update(['skill_point' => (int) $char->skill_point + $pts]);
                $this->logAction($admin->id, 'admin', $accountId, $charId, 'admin.reset_skills', ['skill_point' => $char->skill_point], ['skill_point' => (int) $char->skill_point + $pts], 0, null, 'success', null, $ip);
                return "Character skills reset (+{$pts} skill points returned).";

            case 'approve_recovery':
                $req = CharacterDeletionRequest::where('char_id', $charId)->latest('id')->first();
                if ($req) {
                    $req->update([
                        'status' => 'recovered',
                        'recovery_status' => 'approved',
                        'reviewed_by' => $admin->id,
                        'reviewed_at' => now(),
                    ]);
                }
                if ($this->hasColumn('char', 'delete_date')) {
                    DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->update(['delete_date' => 0]);
                }
                $this->logAction($admin->id, 'admin', $accountId, $charId, 'admin.approve_recovery', null, ['delete_date' => 0], 0, null, 'success', null, $ip);
                return 'Character deletion cancelled / recovered.';

            default:
                throw new RuntimeException("Unknown moderation action: {$action}");
        }
    }

    public function adminGiveItem(User $admin, int $charId, int $itemId, int $amount = 1, int $refine = 0, bool $isBound = false, string $delivery = 'escrow', ?string $ip = null): void
    {
        $charTable = $this->emulator->getTableName('char');
        $char = DB::connection('ragnarok')->table($charTable)->where('char_id', $charId)->first();
        if (!$char) {
            throw new RuntimeException("Character #{$charId} not found.");
        }

        $item = $this->itemRepository->findById($itemId);
        if (!$item) {
            throw new RuntimeException("Item ID #{$itemId} was not found in the configured item database.");
        }

        $itemName = $item->name_english ?? $item->name_aegis ?? "Item #{$itemId}";

        if ($delivery === 'escrow') {
            $this->escrowInboxService->depositItem((int) $char->account_id, [
                'source_type' => 'admin',
                'source_id' => $charId,
                'title' => "Admin Item Delivery ({$char->name})",
                'item_id' => $itemId,
                'item_name' => $itemName,
                'quantity' => $amount,
                'refine' => $refine,
                'bound' => $isBound ? 1 : 0,
                'metadata' => ['target_char_id' => $charId, 'granted_by' => $admin->id],
            ]);
            $this->logAction($admin->id, 'admin', (int) $char->account_id, $charId, 'admin.give_item_escrow', null, ['item_id' => $itemId, 'amount' => $amount, 'refine' => $refine], 0, null, 'success', null, $ip);
            return;
        }

        if ((int) $char->online > 0) {
            throw new RuntimeException('Character must be offline for direct inventory insertion. Use Escrow Inbox delivery instead.');
        }

        $invTable = $this->emulator->getTableName('inventory');
        DB::connection('ragnarok')->table($invTable)->insert([
            'char_id' => $charId,
            'nameid' => $itemId,
            'amount' => $amount,
            'equip' => 0,
            'identify' => 1,
            'refine' => $refine,
            'attribute' => 0,
            'bound' => $isBound ? 1 : 0,
        ]);

        $this->logAction($admin->id, 'admin', (int) $char->account_id, $charId, 'admin.give_item_direct', null, ['item_id' => $itemId, 'amount' => $amount, 'refine' => $refine], 0, null, 'success', null, $ip);
    }

    public function adminModifyContainerItem(User $admin, int $charId, string $container, int $rowId, string $action, array $params = [], ?string $ip = null): void
    {
        $tableMap = [
            'inventory' => ['table' => 'inventory', 'owner_col' => 'char_id'],
            'cart' => ['table' => 'cart_inventory', 'owner_col' => 'char_id'],
            'storage' => ['table' => 'storage', 'owner_col' => 'account_id'],
        ];

        if (!isset($tableMap[$container])) {
            throw new RuntimeException('Invalid container type.');
        }

        $char = DB::connection('ragnarok')->table($this->emulator->getTableName('char'))->where('char_id', $charId)->first();
        if (!$char) {
            throw new RuntimeException("Character #{$charId} not found.");
        }

        if ((int) $char->online > 0) {
            throw new RuntimeException('Character must be offline before modifying inventory, cart, or storage.');
        }

        $table = $this->emulator->getTableName($tableMap[$container]['table']);
        $ownerCol = $tableMap[$container]['owner_col'];
        $ownerVal = $ownerCol === 'account_id' ? (int) $char->account_id : $charId;

        $row = DB::connection('ragnarok')->table($table)->where('id', $rowId)->where($ownerCol, $ownerVal)->first();
        if (!$row) {
            throw new RuntimeException('Item entry not found in target container.');
        }

        if ($action === 'remove') {
            DB::connection('ragnarok')->table($table)->where('id', $rowId)->delete();
            $this->logAction($admin->id, 'admin', (int) $char->account_id, $charId, "admin.{$container}_remove", ['nameid' => $row->nameid, 'amount' => $row->amount], null, 0, null, 'success', null, $ip);
        } elseif ($action === 'unequip' && $container === 'inventory') {
            DB::connection('ragnarok')->table($table)->where('id', $rowId)->update(['equip' => 0]);
            $this->logAction($admin->id, 'admin', (int) $char->account_id, $charId, 'admin.unequip', ['equip' => $row->equip], ['equip' => 0], 0, null, 'success', null, $ip);
        } elseif ($action === 'update') {
            $newAmount = max(1, (int) ($params['amount'] ?? $row->amount));
            $newRefine = max(0, min(20, (int) ($params['refine'] ?? $row->refine)));
            DB::connection('ragnarok')->table($table)->where('id', $rowId)->update([
                'amount' => $newAmount,
                'refine' => $newRefine,
            ]);
            $this->logAction($admin->id, 'admin', (int) $char->account_id, $charId, "admin.{$container}_update", ['amount' => $row->amount, 'refine' => $row->refine], ['amount' => $newAmount, 'refine' => $newRefine], 0, null, 'success', null, $ip);
        }
    }

    public function adminRefundTransaction(User $admin, int $transactionId, string $reason, ?string $ip = null): void
    {
        $tx = CharacterServiceTransaction::findOrFail($transactionId);
        if ($tx->status === 'refunded') {
            throw new RuntimeException('Transaction has already been refunded.');
        }
        if ((int) $tx->cash_points_cost <= 0) {
            throw new RuntimeException('This transaction had zero Cash Point cost.');
        }

        $this->cashPointsService->credit(
            (int) $tx->account_id,
            (int) $tx->cash_points_cost,
            "Refund for character service ({$tx->service_type}): {$reason}",
            'char_svc_refund_' . $tx->id
        );

        $tx->update([
            'status' => 'refunded',
            'refunded_by' => $admin->id,
            'refunded_at' => now(),
        ]);

        $this->logAction($admin->id, 'admin', (int) $tx->account_id, (int) $tx->char_id, 'admin.refund', ['status' => 'completed'], ['status' => 'refunded', 'reason' => $reason], (int) $tx->cash_points_cost, $tx->idempotency_key, 'success', null, $ip);
    }

    public function addAdminNote(User $admin, int $charId, string $note): CharacterAdminNote
    {
        $created = CharacterAdminNote::create([
            'char_id' => $charId,
            'admin_user_id' => $admin->id,
            'note' => trim($note),
        ]);
        $this->logAction($admin->id, 'admin', null, $charId, 'admin.note_added', null, ['note_id' => $created->id]);
        return $created;
    }

    private function guardPlayerService(object $char, string $serviceType, int $cooldownHours, int $minLevel = 1, int $maxLevel = 999): void
    {
        if ((int) $char->online > 0) {
            throw new RuntimeException('Your character must be logged out of the game before using this service.');
        }

        $activeLock = CharacterLock::where('char_id', (int) $char->char_id)->where('is_locked', true)->first();
        if ($activeLock) {
            throw new RuntimeException('This character is currently locked: ' . $activeLock->reason);
        }

        if ((int) $char->base_level < $minLevel || (int) $char->base_level > $maxLevel) {
            throw new RuntimeException("Character Base Level ({$char->base_level}) does not meet the requirement ({$minLevel} - {$maxLevel}).");
        }

        if ($cooldownHours > 0) {
            $last = CharacterServiceTransaction::where('char_id', (int) $char->char_id)
                ->where('service_type', $serviceType)
                ->where('status', 'completed')
                ->latest('created_at')
                ->first();
            if ($last && $last->created_at->copy()->addHours($cooldownHours)->isFuture()) {
                $until = $last->created_at->copy()->addHours($cooldownHours)->format('M d, Y H:i');
                throw new RuntimeException("This service is on cooldown until {$until}.");
            }
        }
    }

    private function executePaidService(
        User $user,
        object $char,
        string $serviceType,
        int $cost,
        string $idempotencyKey,
        array $oldValues,
        array $newValues,
        callable $mutation,
        ?string $ip = null
    ): CharacterServiceTransaction {
        $existing = CharacterServiceTransaction::where('idempotency_key', $idempotencyKey)->first();
        if ($existing) {
            return $existing;
        }

        $accountId = (int) $char->account_id;
        $charId = (int) $char->char_id;

        if ($cost > 0) {
            $balance = $this->cashPointsService->getBalance($accountId);
            if ($balance < $cost) {
                throw new RuntimeException("Insufficient Cash Points. Required: {$cost}, Available: {$balance}.");
            }
        }

        $tx = CharacterServiceTransaction::create([
            'idempotency_key' => $idempotencyKey,
            'user_id' => $user->id,
            'account_id' => $accountId,
            'char_id' => $charId,
            'char_name' => $char->name,
            'service_type' => $serviceType,
            'cash_points_cost' => $cost,
            'status' => 'pending',
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ip,
        ]);

        try {
            if ($cost > 0) {
                $this->cashPointsService->deduct($accountId, $cost, "Character Service: {$serviceType} ({$char->name})", $idempotencyKey);
            }

            $mutation();

            $tx->update(['status' => 'completed']);
            $this->logAction($user->id, 'player', $accountId, $charId, "service.{$serviceType}", $oldValues, $newValues, $cost, $idempotencyKey, 'success', null, $ip);
            ActivityLog::log("char_svc.{$serviceType}", "Used {$serviceType} on {$char->name} (#{$charId}) for {$cost} Cash Points.", $accountId);
        } catch (\Throwable $e) {
            if ($cost > 0) {
                try {
                    $this->cashPointsService->credit($accountId, $cost, "Auto-rollback failed {$serviceType}", $idempotencyKey . '_rollback');
                } catch (\Throwable) {
                }
            }
            $tx->update(['status' => 'failed', 'failure_reason' => $e->getMessage()]);
            $this->logAction($user->id, 'player', $accountId, $charId, "service.{$serviceType}", $oldValues, $newValues, $cost, $idempotencyKey, 'failed', $e->getMessage(), $ip);
            throw $e;
        }

        return $tx;
    }

    private function validateCharacterName(string $name, string $blockedWordsCsv): void
    {
        if (strlen($name) < 4 || strlen($name) > 23) {
            throw new RuntimeException('Character name must be between 4 and 23 characters.');
        }

        if (!preg_match('/^[A-Za-z0-9_\-\[\] ]+$/', $name)) {
            throw new RuntimeException('Character name contains unsupported characters.');
        }

        $blocked = array_filter(array_map('trim', explode(',', strtolower($blockedWordsCsv))));
        $lower = strtolower($name);
        foreach ($blocked as $word) {
            if ($word !== '' && str_contains($lower, $word)) {
                throw new RuntimeException("Character name contains a prohibited word ({$word}).");
            }
        }
    }

    private function computeStatResetPayload(object $char): array
    {
        $baseStats = ['str', 'agi', 'vit', 'int', 'dex', 'luk'];
        $refundedStatusPoints = 0;
        $oldValues = ['status_point' => (int) $char->status_point];
        $payload = [];

        foreach ($baseStats as $stat) {
            $val = max(1, (int) ($char->{$stat} ?? 1));
            $oldValues[$stat] = $val;
            for ($s = 1; $s < $val; $s++) {
                $refundedStatusPoints += $s < 100
                    ? (intdiv($s - 1, 10) + 2)
                    : (4 * intdiv($s - 100, 5) + 16);
            }
            $payload[$stat] = 1;
        }

        $newStatusPoint = (int) $char->status_point + $refundedStatusPoints;
        $payload['status_point'] = $newStatusPoint;
        $newValues = array_merge($payload, ['refunded_status_points' => $refundedStatusPoints]);

        // Renewal 4th job trait stats if present
        $traits = ['pow', 'sta', 'wis', 'spl', 'con', 'crt'];
        if ($this->hasColumn('char', 'trait_point')) {
            $refundedTraits = 0;
            foreach ($traits as $t) {
                if ($this->hasColumn('char', $t)) {
                    $tVal = max(0, (int) ($char->{$t} ?? 0));
                    $oldValues[$t] = $tVal;
                    $refundedTraits += $tVal;
                    $payload[$t] = 0;
                    $newValues[$t] = 0;
                }
            }
            $payload['trait_point'] = (int) ($char->trait_point ?? 0) + $refundedTraits;
            $newValues['trait_point'] = $payload['trait_point'];
        }

        return [$oldValues, $newValues, $payload];
    }

    private function getRawItems(string $tableKey, string $ownerColumn, int $ownerId): array
    {
        return DB::connection('ragnarok')
            ->table($this->emulator->getTableName($tableKey))
            ->where($ownerColumn, $ownerId)
            ->orderByDesc('equip')
            ->orderBy('id')
            ->get()
            ->all();
    }

    private function resolveItemMeta(int $itemId): array
    {
        $item = $this->itemRepository->findById($itemId);
        if ($item) {
            $name = $item->name_english ?? $item->name_japanese ?? $item->name_aegis ?? null;
            $slots = (int) ($item->slots ?? 0);
            if ($name) {
                return [
                    'name' => $name,
                    'slots' => $slots,
                    'type' => strtolower((string) ($item->type ?? 'etc')),
                ];
            }
        }

        $known = [
            501 => ['Red Potion', 0, 'healing'],
            502 => ['Orange Potion', 0, 'healing'],
            503 => ['Yellow Potion', 0, 'healing'],
            504 => ['White Potion', 0, 'healing'],
            505 => ['Blue Potion', 0, 'healing'],
            522 => ['Mastela Fruit', 0, 'healing'],
            601 => ['Fly Wing', 0, 'usable'],
            602 => ['Butterfly Wing', 0, 'usable'],
            607 => ['Yggdrasil Berry', 0, 'healing'],
            608 => ['Yggdrasil Seed', 0, 'healing'],
            1107 => ['Blade', 4, 'weapon'],
            1161 => ['Balmung', 0, 'weapon'],
            1220 => ['Ice Pick', 1, 'weapon'],
            2101 => ['Guard', 1, 'armor'],
            2115 => ["Valkyrja's Shield", 1, 'armor'],
            2357 => ['Valkyrian Armor', 1, 'armor'],
            2421 => ['Valkyrian Shoes', 1, 'armor'],
            2524 => ['Valkyrian Manteau', 1, 'armor'],
            2620 => ['Ring of Flame Lord', 0, 'armor'],
            4001 => ['Poring Card', 0, 'card'],
            4058 => ['Thara Frog Card', 0, 'card'],
            4064 => ['Zerom Card', 0, 'card'],
            4121 => ['Phreeoni Card', 0, 'card'],
            4128 => ['Golden Thief Bug Card', 0, 'card'],
            4131 => ['Moonlight Flower Card', 0, 'card'],
            4133 => ['Raydric Card', 0, 'card'],
            4140 => ['Abysmal Knight Card', 0, 'card'],
            5013 => ["Lord Kaho's Horn", 0, 'armor'],
            5123 => ["Ulle's Cap", 1, 'armor'],
        ];

        if (isset($known[$itemId])) {
            return [
                'name' => $known[$itemId][0],
                'slots' => $known[$itemId][1],
                'type' => $known[$itemId][2],
            ];
        }

        return \Illuminate\Support\Facades\Cache::remember("ro_item_meta_{$itemId}", 86400 * 30, function () use ($itemId) {
            try {
                $res = \Illuminate\Support\Facades\Http::timeout(2)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get("https://www.divine-pride.net/database/item/{$itemId}");
                if ($res->successful() && preg_match('/<title>(.*?)\s*(?:-|—|\|)\s*Divine Pride/i', $res->body(), $m)) {
                    $title = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
                    $slots = 0;
                    if (preg_match('/\[(\d+)\]$/', $title, $sm)) {
                        $slots = (int) $sm[1];
                        $title = trim(preg_replace('/\s*\[\d+\]$/', '', $title));
                    }
                    if ($title !== '') {
                        return ['name' => $title, 'slots' => $slots, 'type' => 'etc'];
                    }
                }
            } catch (\Throwable) {
            }

            return ['name' => "Item #{$itemId}", 'slots' => 0, 'type' => 'etc'];
        });
    }

    private function enrichItems(array $rows): array
    {
        foreach ($rows as $row) {
            $meta = $this->resolveItemMeta((int) $row->nameid);
            $displayName = $meta['name'];
            if ($meta['slots'] > 0 && !str_contains($displayName, '[')) {
                $displayName .= " [{$meta['slots']}]";
            }

            $cards = [];
            foreach (['card0', 'card1', 'card2', 'card3'] as $cCol) {
                $cid = (int) ($row->{$cCol} ?? 0);
                if ($cid > 0 && $cid !== 254 && $cid !== 255) {
                    $cMeta = $this->resolveItemMeta($cid);
                    $cards[] = $cMeta['name'];
                }
            }

            $row->item_name = $displayName;
            $row->item_slots = $meta['slots'];
            $row->cards = $cards;
            $row->item_type = $meta['type'];
            $row->icon_url = "https://static.divine-pride.net/images/items/item/{$row->nameid}.png";
            $row->equip_slot_name = (int) ($row->equip ?? 0) > 0 ? JobHelper::getEquipSlotName((int) $row->equip) : null;
        }
        return $rows;
    }

    private function logAction(
        ?int $actorUserId,
        string $actorType,
        ?int $accountId,
        int $charId,
        string $actionType,
        ?array $previous = null,
        ?array $updated = null,
        int $cost = 0,
        ?string $txRef = null,
        string $result = 'success',
        ?string $failureReason = null,
        ?string $ip = null
    ): void {
        CharacterServiceLog::create([
            'actor_user_id' => $actorUserId,
            'actor_type' => $actorType,
            'account_id' => $accountId,
            'char_id' => $charId,
            'action_type' => $actionType,
            'previous_values' => $previous,
            'updated_values' => $updated,
            'service_cost' => $cost,
            'transaction_reference' => $txRef,
            'result' => $result,
            'failure_reason' => $failureReason,
            'ip_address' => $ip,
        ]);
    }
}
