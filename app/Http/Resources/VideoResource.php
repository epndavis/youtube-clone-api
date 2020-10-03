<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
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
            'view_count_shorthand' => $this->shortViewCount($viewCount),
            'view_count' => number_format($viewCount),
            'uploaded_when' => $this->created_at->diffForHumans(),
            'thumb' => $video->getFullUrl('thumb'),
            'gif' => '',
            'duration' => '28',
            'channel' => [
                'name' => $this->channel->name,
            ],
        ];
    }
}
