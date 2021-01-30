<?php

namespace Tests\Feature;

use App\Models\Channel;
use Tests\TestCase;
use App\Models\Video;

class VideoApiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_json_list_of_videos_from_api()
    {
        $response = $this->get('/api/videos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [ 
                        'title', 
                        'description',
                        'view_count_shorthand',
                        'view_count',
                        'uploaded_when',
                        'thumb',
                        'duration',
                        'channel' => [
                            'name',
                        ],
                    ],
                ]
            ]);
    }

    /** @test */
    public function get_single_video_from_api()
    {
        $video = Video::inRandomOrder()->first();

        $response = $this->get('/api/videos/' . $video->uuid);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'video',
                    'related',
                ],
            ]);
    }

    public function an_authenticated_user_can_post_a_video()
    {
        $channel = Channel::factory()->create();
    }
}
