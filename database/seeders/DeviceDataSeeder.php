<?php

namespace Database\Seeders;

use App\Models\DeviceData;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DeviceDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        $since = Carbon::parse('2021-06-21 00:15:53');
        $until = Carbon::now();

        $interval = collect(CarbonPeriod::since($since)->minutes(5)->until($until));
        $faker = Factory::create();

        $user = User::whereEmail('y@gizcan.xyz')->first();
        foreach ($user->devices as $device) {
            foreach ($device->parameters as $parameter) {
                foreach ($interval as $time) {
                    DeviceData::create([
                        'device_parameter_id' => $parameter->parameters->id,
                        'device_id' => $device->id,
                        'value' => $faker->numberBetween(25, 40),
                        'created_at' => $time,
                    ]);
                }
            }
        }
    }
}
