<?php

namespace App\Services;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use App\Models\Video;
use Illuminate\Support\Str;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\Dimension;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VideoService 
{
    const THUMB_WIDTH = 368;

    const THUMB_HEIGHT = 232;

    public function addMedia(Video $video, $filePath)
    {
        $duration = $this->getFFProbe()->format($filePath)->get('duration');
        $name = Str::uuid();

        if ($filePath instanceof UploadedFile) {
            $extension = $filePath->getClientOriginalExtension();
        } else {
            $extension = pathinfo($filePath)['extension'];
        }

        $media = $video->addMedia($filePath)
            ->usingName($name)
            ->usingFileName($name . '.' . $extension)
            ->withCustomProperties([
                'info' => [
                    'duration' => $duration,
                ]
            ])
            ->toMediaCollection('video');

        $this->generateGif($media);

        $this->generateQuality($media);
    }

    /**
     * @param Media $media
     * @param string $type
     * 
     * @return string
     */
    public function getUrl(Media $media, $type = '')
    {
        if ($media->disk === 's3') {
            if ($type === 'gif') {
                return Storage::disk($media->disk)->temporaryUrl($this->gifPath($media), now()->addMinutes(10));
            }

            return $media->getTemporaryUrl(now()->addMinutes(10), $type);
        }

        if ($type === 'gif') {
            return Storage::disk($media->disk)->url($this->gifPath($media));
        }

        return $media->getUrl($type);
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
        $file = $ffmpeg->open($this->getUrl($media));

        $gif = $file->gif(TimeCode::fromSeconds($seconds), (new Dimension(static::THUMB_WIDTH, static::THUMB_HEIGHT)), $duration);
        $gif->save(storage_path('app/temp/' . Str::slug($media->name) . '-gif.gif'));

        Storage::disk($media->disk)->put($this->gifPath($media), Storage::get('temp/' . Str::slug($media->name) . '-gif.gif'));
        Storage::delete('temp/' . Str::slug($media->name) . '-gif.gif');

        $media->setCustomProperty('gif_generated', true);
        $media->save();
    }

    public function generateQuality(Media $media)
    {
        $ffmpeg = $this->getFFMpeg();
        $file = $ffmpeg->open($this->getUrl($media));

        // $file
        //     ->filters()
        //     ->resize(new \FFMpeg\Coordinate\Dimension(1200, 720))
        //     ->synchronize();
        
        // $file
        //     ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        //     ->save('frame.jpg');
        $format = new \FFMpeg\Format\Video\X264('copy');
        $format->setKiloBitrate(2000);

        $file->save($format, storage_path('app/temp/' . Str::slug($media->name) . '.mp4'));

        Storage::disk($media->disk)->put(app(Filesystem::class)->getMediaDirectory($media) . Str::slug($media->name) . '.mp4', Storage::get('temp/' . Str::slug($media->name) . '.mp4'));
        Storage::delete('temp/' . Str::slug($media->name) . '.mp4');
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
