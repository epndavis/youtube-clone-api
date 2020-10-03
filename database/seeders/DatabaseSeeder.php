<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Video;
use App\Models\Channel;
use Faker\Factory;
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
                    ->has(Video::factory()->count(3))
            )
            ->create();

        foreach ($users as $user) {
            foreach ($user->channels as $channel) {
                foreach ($channel->videos as $video) {
                    $path = Factory::create()->file(base_path('/tmp/videos'), base_path('/tmp'));
                    
                    $video->addMedia($path)
                        ->toMediaCollection('video');
                }
            }
        }
    }
}
