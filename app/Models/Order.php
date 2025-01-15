<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'email',
        'postal_code',
        'street_name',
        'house_number',
        'city',
        'total_price',
        'items',
        // 'unit_price',
        // 'combined_unit_price',
    ];

    protected $casts = [
        'items' => 'array',
        'total_price' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
