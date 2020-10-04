<?php

namespace App\Services;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use App\Models\Video;
use Illuminate\Support\Str;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\Dimension;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VideoService 
{
    const THUMB_WIDTH = 368;

    const THUMB_HEIGHT = 232;

    public function addMedia(Video $video, $filePath)
    {
        $videoFile = $this->getFFProbe()
            ->streams($filePath)
            ->videos()                   
            ->first(); 

        $duration = $videoFile->get('duration');

        $media = $video->addMedia($filePath)
            ->withCustomProperties([
                'info' => [
                    'duration' => $duration,
                ],
            ])
            ->toMediaCollection('video');

        $this->generateGif($media);
    }

    /**
     * @param Media $media
     * @param int $seconds
     * @param int $duration
     * 
     * @return string $path
     */
    public function generateGif(Media $media, $seconds = 0, $duration = 3)
    {
        $ffmpeg = $this->getFFMpeg();
        $file = $ffmpeg->open($media->getPath());

        $path = $this->getGifPath($media);

        $gif = $file->gif(TimeCode::fromSeconds($seconds), (new Dimension(static::THUMB_WIDTH, static::THUMB_HEIGHT)), $duration);
        $gif->save($path);

        return $path;
    }

    /**
     * @param Media $media
     * 
     * @return string
     */
    public function getGifUrl(Media $media)
    {
        return Storage::disk(config('media-library.disk_name'))
            ->url($this->gifPath($media));
    }

    /**
     * @param Media $media
     * 
     * @return string
     */
    public function getGifPath(Media $media)
    {
        return Storage::disk(config('media-library.disk_name'))
            ->path($this->gifPath($media));
    }

    /**
     * @param Media $media
     * 
     * @return string
     */
    public function gifPath(Media $media)
    {
        return app(Filesystem::class)->getConversionDirectory($media) . Str::slug($media->name) . '-gif.gif';
    }

    /**
     * @return FFMpeg
     */
    private function getFFMpeg()
    {
        return FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);
    }

    /**
     * @return FFProbe
     */
    private function getFFProbe()
    {
        return FFProbe::create([
            'ffmpeg.binaries'  => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);
    }
}
