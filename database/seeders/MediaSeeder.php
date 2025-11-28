<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            Media::factory(5)->create();

            return;
        }

        Media::factory(15)
            ->state(fn () => ['user_id' => $users->random()->id])
            ->create();
    }
}

