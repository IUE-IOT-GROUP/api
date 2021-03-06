<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereEmail('y@gizcan.xyz')->first();
        $parent = $user->places()->create([
            'name' => 'Home',
            'parent_id' => null
        ]);

        $kitchen = $parent->children()->create([
            'name' => 'Kitchen',
            'user_id' => $user->id,
        ]);

        $living = $parent->children()->create([
            'name' => 'Living Room',
            'user_id' => $user->id,
        ]);

        $room = $parent->children()->create([
            'name' => 'Bedroom',
            'user_id' => $user->id,
        ]);
    }
}
