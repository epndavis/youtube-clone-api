<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Models\Channel;
use App\Services\VideoService;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Resources\SingleVideoResource;

class VideoController extends Controller {
    
    /**
     * Display a listing of the resource.
     *
     * @return VideoResource
     */
    public function index() {
        return VideoResource::collection(
            Video::has('media')->get()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  string $uuid
     * 
     * @return SingleVideoResource
     */
    public function show($uuid)
    {
        return new SingleVideoResource(
            Video::where('uuid', $uuid)
                ->first()
        );
    }

    /**
     * @param StoreVideoRequest $request
     * @param VideoService $service
     * 
     * @return SingleVideoResource $video
     */
    public function store(StoreVideoRequest $request, VideoService $service)
    {
        $video = new Video();

        $video->title = $request->input('title');
        $video->description = $request->input('description');
        $video->channel_id = Channel::inRandomOrder()->first()->id;

        $video->save();
        
        $service->addMedia($video, $request->file('video'));

        return new SingleVideoResource($video);
    }
}
