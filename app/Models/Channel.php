<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $casts = [
        'verified' => 'boolean',
    ];

    /**
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function videos() {
        return $this->hasMany(Video::class);
    }
}
