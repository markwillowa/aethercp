<?php

namespace App\Services\Forum;

use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Models\ForumUserProfile;
use App\Models\User;
use App\Services\Emulator\JobHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CharacterIdentityService
{
    public function __construct(
        protected CharacterRepositoryInterface $characterRepo
    ) {}

    /**
     * Get all Ragnarok characters owned by the given user's account_id.
     */
    public function getOwnedCharacters(User $user): array
    {
        if (!$user->account_id) {
            return [];
        }

        try {
            $chars = $this->characterRepo->getByAccountId((int) $user->account_id);
            return array_map(fn ($c) => $this->formatCharacter((array) $c), $chars);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Verify server-side that the given char_id belongs to the user's account_id.
     */
    public function verifyCharacterOwnership(User $user, int $charId): ?array
    {
        if (!$user->account_id || $charId <= 0) {
            return null;
        }

        foreach ($this->getOwnedCharacters($user) as $char) {
            if ((int) ($char['char_id'] ?? 0) === $charId) {
                return $char;
            }
        }

        return null;
    }

    /**
     * Update a user's forum identity mode and optional character selection.
     */
    public function updateIdentity(User $user, string $mode, ?int $charId = null): ForumUserProfile
    {
        $profile = ForumUserProfile::forUser($user->id);

        if ($mode === 'character') {
            if (!$charId) {
                throw new \InvalidArgumentException('Please select one of your characters.');
            }
            $owned = $this->verifyCharacterOwnership($user, $charId);
            if (!$owned) {
                throw new \InvalidArgumentException('Selected character does not belong to your account.');
            }
            $profile->identity_mode = 'character';
            $profile->selected_char_id = $charId;
        } else {
            $profile->identity_mode = 'username';
        }

        $profile->save();

        return $profile;
    }

    /**
     * Batch-resolve public forum identities for a collection of Users (zero N+1).
     */
    public function resolveBatch(Collection $users, ReputationService $reputationService): array
    {
        $uniqueUsers = $users->filter()->unique('id');
        if ($uniqueUsers->isEmpty()) {
            return [];
        }

        $userIds = $uniqueUsers->pluck('id')->all();
        $profiles = ForumUserProfile::whereIn('user_id', $userIds)->get()->keyBy('user_id');

        $charIdsToFetch = $profiles
            ->filter(fn ($p) => $p->identity_mode === 'character' && $p->selected_char_id)
            ->pluck('selected_char_id')
            ->unique()
            ->values()
            ->all();

        $charactersById = $this->fetchCharactersByIds($charIdsToFetch);

        $resolved = [];
        foreach ($uniqueUsers as $user) {
            $profile = $profiles->get($user->id);
            $points = $profile ? (int) $profile->reputation_points : 0;
            $level = $reputationService->getLevelForPoints($points);

            $character = null;
            if ($profile && $profile->identity_mode === 'character' && $profile->selected_char_id) {
                $charRow = $charactersById[$profile->selected_char_id] ?? null;
                // Extra safety: verify character's account_id matches user's account_id
                if ($charRow && $user->account_id && (int) $charRow['account_id'] === (int) $user->account_id) {
                    $character = $charRow;
                }
            }

            $resolved[$user->id] = [
                'user_id' => $user->id,
                'username' => $user->username ?? $user->name,
                'role' => $user->role,
                'is_staff' => $user->isStaff(),
                'avatar' => $user->avatar,
                'identity_mode' => $character ? 'character' : 'username',
                'character' => $character,
                'reputation_points' => $points,
                'reputation_level' => $level,
            ];
        }

        return $resolved;
    }

    protected function fetchCharactersByIds(array $charIds): array
    {
        if (empty($charIds)) {
            return [];
        }

        try {
            $charTable = config('emulator.tables.char', 'char');
            $rows = DB::connection('ragnarok')
                ->table($charTable)
                ->select('char_id', 'account_id', 'name', 'class', 'base_level', 'job_level', 'sex')
                ->whereIn('char_id', $charIds)
                ->get();

            $map = [];
            foreach ($rows as $row) {
                $formatted = $this->formatCharacter((array) $row);
                $map[(int) $formatted['char_id']] = $formatted;
            }
            return $map;
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function formatCharacter(array $char): array
    {
        $jobId = (int) ($char['class'] ?? 0);
        $sex = (string) ($char['sex'] ?? 'M');

        return [
            'char_id' => (int) ($char['char_id'] ?? 0),
            'account_id' => (int) ($char['account_id'] ?? 0),
            'name' => (string) ($char['name'] ?? 'Unknown'),
            'class' => $jobId,
            'job_name' => JobHelper::getName($jobId),
            'badge_color' => JobHelper::getBadgeColor($jobId),
            'sprite_url' => JobHelper::getSpriteUrl($jobId, $sex),
            'base_level' => (int) ($char['base_level'] ?? 1),
            'job_level' => (int) ($char['job_level'] ?? 1),
        ];
    }
}
