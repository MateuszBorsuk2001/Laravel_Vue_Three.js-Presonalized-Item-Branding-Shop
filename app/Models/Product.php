<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $name
 * @property integer $image_id
 * @property integer $user_id
 * @method static where(string $string, string $string1)
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_id',
        'user_id',
    ];

    /**
     * Związek z modelem Image.
     *
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    /**
     * Związek z modelem User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
