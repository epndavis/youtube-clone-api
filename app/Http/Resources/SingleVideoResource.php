<?php

namespace App\Http\Resources;

use App\Models\Video;
use App\Services\VideoService;
use App\Http\Resources\VideoResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleVideoResource extends JsonResource
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
            'video' => [
                'id' => $this->uuid,
                'title' => $this->title,
                'description' => $this->description,
                'view_count_shorthand' => $this->shortViewCount($viewCount),
                'view_count' => number_format($viewCount),
                'uploaded_when' => $this->created_at->diffForHumans(),
                'src' => $this->service->getUrl($video),
                'thumb' => $this->service->getUrl($video, 'thumb'),
                'duration' => $video->getCustomProperty('info')['duration'],
                'channel' => [
                    'name' => $this->channel->name,
                    'verified' => $this->channel->verified,
                ],
            ],

            'related' => VideoResource::collection(
                Video::has('media')
                    ->where('uuid', '!=', $this->uuid)
                    ->inRandomOrder()
                    ->limit(20)
                    ->get()
            ),
        ];
    }
}
