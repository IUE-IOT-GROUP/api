<?php

namespace Database\Seeders;

use App\Models\DeviceData;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DeviceDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        $user = User::find(1);
        foreach ($user->devices as $device) {
            foreach ($device->parameters as $parameter)
            {
                foreach (range(0, 50) as $i)
                {
                    DeviceData::create([
                        'parameter_type_user_device_id' => $parameter->parameters->id,
                        'user_device_id' => $device->id,
                        'value' => $faker->randomNumber(2),
                    ]);
                }
            }
        }
    }
}
