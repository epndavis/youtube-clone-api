<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            $uuid = (string) Str::uuid();
            
            while (self::where('uuid', $uuid)->exists()) {
                $uuid = (string) Str::uuid();
            }

            $video->uuid = $uuid;
        });
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('video')
            ->singleFile();
    }

    /**
     * @return void
     */
    public function registerMediaConversions(): void
    {
        $this->addMediaConversion('thumb')
              ->width(368)
              ->height(232)
              ->extractVideoFrameAtSecond(1);
    }

    /**
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    /**
     * @param int $num
     * 
     * @return string
     */
    public function shortViewCount($num) 
    {
        for ($i = 0; $num >= 1000; $i++) {
            $num /= 1000;
        }

        return round($num, 1) . ['', 'K', 'M', 'B', 'T'][$i];
    }
}
