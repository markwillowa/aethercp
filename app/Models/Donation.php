<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'account_id',
        'package_id',
        'amount',
        'currency',
        'points_awarded',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'points_awarded' => 'integer',
        'account_id' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function package()
    {
        return $this->belongsTo(DonationPackage::class, 'package_id');
    }
}

