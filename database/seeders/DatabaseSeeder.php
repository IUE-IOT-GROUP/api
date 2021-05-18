<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\User;
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
         User::create([
             'name' => 'yağız',
             'email' => 'y@gizcan.xyz',
             'password' => \Hash::make('1234')
         ]);

         User::create([
             'name' => 'erel',
             'email' => 'erel@ozturk.com',
             'password' => \Hash::make('1234')
         ]);

        $this->call([
            PlaceSeeder::class,
            DeviceSeeder::class,
        ]);
    }
}
