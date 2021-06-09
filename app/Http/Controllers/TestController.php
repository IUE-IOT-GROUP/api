<?php

namespace App\Http\Controllers;

use App\Models\DeviceData;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Faker\Factory;

class TestController extends Controller
{
    public function __invoke()
    {

        $since = Carbon::now()->startOfDay();
        $until = Carbon::create(null, null, null, hour: 21);

        $interval = collect(CarbonPeriod::since($since)->minutes(60)->until($until));
        $faker = Factory::create();

        $user = User::find(1);
        foreach ($user->devices as $device) {
            foreach ($device->parameters as $parameter) {
                foreach ($interval as $time) {
                    DeviceData::create([
                        'parameter_type_user_device_id' => $parameter->parameters->id,
                        'user_device_id' => $device->id,
                        'value' => $faker->randomNumber(2),
                        'created_at' => $time,
                    ]);
                }
            }
        }

    }
}
