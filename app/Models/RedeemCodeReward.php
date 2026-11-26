<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedeemCodeReward extends Model
{
    protected $fillable = [
        'campaign_id',
        'redeem_code_id',
        'reward_type',
        'amount',
        'delivery_scope',
        'item_id',
        'item_name',
        'refine',
        'card0',
        'card1',
        'card2',
        'card3',
        'is_bound',
        'custom_payload',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'integer',
        'item_id' => 'integer',
        'refine' => 'integer',
        'card0' => 'integer',
        'card1' => 'integer',
        'card2' => 'integer',
        'card3' => 'integer',
        'is_bound' => 'boolean',
        'custom_payload' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(RedeemCampaign::class, 'campaign_id');
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(RedeemCode::class, 'redeem_code_id');
    }

    public function formattedLabel(): string
    {
        return match ($this->reward_type) {
            'cash_points' => number_format($this->amount) . ' Cash Points',
            'zeny' => number_format($this->amount) . ' Zeny',
            'item' => ($this->refine > 0 ? "+{$this->refine} " : '') . ($this->item_name ?: "Item #{$this->item_id}") . " x{$this->amount}",
            default => (string) ($this->custom_payload['label'] ?? 'Custom Reward'),
        };
    }
}
