<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\User;
use App\Models\Video;
use App\Models\Channel;
use App\Services\VideoService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory(3)
            ->has(
                Channel::factory()
            )
            ->create();

        foreach ($users as $user) {
            foreach ($user->channels as $channel) {
                Video::factory()
                    ->count(3)
                    ->afterCreating(function ($video) {
                        app(VideoService::class)->addMedia(
                            $video, 
                            Factory::create()->file(
                                base_path('/tmp/videos'), 
                                base_path('/tmp/videos/tmp')
                            )
                        );
                    })
                    ->create([
                        'channel_id' => $channel->id,
                    ]);
            }
        }
    }
}
