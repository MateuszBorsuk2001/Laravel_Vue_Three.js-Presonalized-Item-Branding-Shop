<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $url
 * @property string $keep
 * @property string $description
 * @property number $imageable_id
 * @property string $imageable_type
 * @method static where(string $string, string $string1)
 */
class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'keep',
        'description',
        'imageable_id',
        'imageable_type',
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}

