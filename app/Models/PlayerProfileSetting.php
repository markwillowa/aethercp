<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerProfileSetting extends Model
{
    protected $primaryKey = 'char_id';
    public $incrementing = false;

    protected $fillable = [
        'char_id',
        'account_id',
        'is_public',
        'show_char_info',
        'show_equipments',
        'show_stats',
        'show_zeny',
        'show_rankings',
        'show_badges',
        'show_achievements',
        'show_guild',
        'show_last_login',
        'avatar_path',
        'bio',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'show_char_info' => 'boolean',
        'show_equipments' => 'boolean',
        'show_stats' => 'boolean',
        'show_zeny' => 'boolean',
        'show_rankings' => 'boolean',
        'show_badges' => 'boolean',
        'show_achievements' => 'boolean',
        'show_guild' => 'boolean',
        'show_last_login' => 'boolean',
    ];

    public static function forCharacter(int $charId, int $accountId): self
    {
        return self::firstOrCreate(
            ['char_id' => $charId],
            [
                'account_id' => $accountId,
                'is_public' => true,
                'show_char_info' => true,
                'show_equipments' => true,
                'show_stats' => true,
                'show_zeny' => true,
                'show_rankings' => true,
                'show_badges' => true,
                'show_achievements' => true,
                'show_guild' => true,
                'show_last_login' => false,
            ]
        );
    }
}
