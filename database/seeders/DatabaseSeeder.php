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
             'password' => \Hash::make('1234'),
             'is_admin' => true,
         ]);

         User::create([
             'name' => 'erel',
             'email' => 'erel@ozturk.com',
             'password' => \Hash::make('1234'),
             'is_admin' => true,
         ]);

        User::create([
            'name' => 'berkin',
            'email' => 'berkin.yildiran@yahoo.com',
            'password' => \Hash::make('1234'),
            'is_admin' => true,
        ]);

        $call = [];

        if (isFog())
        {
            $call = [
//                PlaceSeeder::class,
//                DeviceSeeder::class,
            ];
        }

        $this->call($call);
    }
}
