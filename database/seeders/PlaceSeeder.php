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
        $parent = Place::create([
            'name' => 'Home',
            'user_id' => 1,
            'parent_id' => null
        ]);

        $kitchen = $parent->children()->create([
            'name' => 'Kitchen',
            'user_id' => 1,
        ]);

        $living = $parent->children()->create([
            'name' => 'Living Room',
            'user_id' => 1
        ]);

        $room = $parent->children()->create([
            'name' => 'Bedroom',
            'user_id' => 1,
        ]);
    }
}
