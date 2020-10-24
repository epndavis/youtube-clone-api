<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use App\Services\VideoService;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    protected VideoService $service;

    /**
     * @param mixed $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->service = app(VideoService::class);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $video = $this->media->first();
        $viewCount = mt_rand(100000, 2000000);

        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'description_short' => Str::limit($this->description, 150),
            'view_count_shorthand' => $this->shortViewCount($viewCount),
            'view_count' => number_format($viewCount),
            'uploaded_when' => $this->created_at->diffForHumans(),
            'thumb' => $this->service->getUrl($video, 'thumb'),
            'gif' => $this->when(($video->getCustomProperty('gif_generated', false)), function() use ($video) {
                return $this->service->getUrl($video, 'gif');
            }),
            'duration' => $video->getCustomProperty('info')['duration'],
            'channel' => [
                'name' => $this->channel->name,
                'verified' => $this->channel->verified,
            ],
        ];
    }
}
