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

        $since = Carbon::parse('2021-06-13 02:53:11');
        $until = Carbon::now();

        $interval = collect(CarbonPeriod::since($since)->minutes(5)->until($until));
        $faker = Factory::create();

        $user = User::find(1);
        foreach ($user->devices as $device) {
            foreach ($device->parameters as $parameter) {
                foreach ($interval as $time) {
                    DeviceData::create([
                        'device_parameter_id' => $parameter->parameters->id,
                        'user_device_id' => $device->id,
                        'value' => $faker->numberBetween(25, 40),
                        'created_at' => $time,
                    ]);
                }
            }
        }
    }
}
