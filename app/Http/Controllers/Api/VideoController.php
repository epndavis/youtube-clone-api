<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
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
}
