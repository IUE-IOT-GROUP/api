<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceDataResource;
use App\Models\DeviceData;
use App\Models\ParameterType;
use App\Models\User;
use App\Models\UserDevice;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Faker\Factory;

class TestController extends Controller
{
    public function add_device_data()
    {
        $since = Carbon::now()->subMonths();
        $until = Carbon::now()->addHours(3);

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

    public function getDeviceData(UserDevice $device)
    {
        $data = [];
        $device->parameters->each(function (ParameterType $type) use (&$data) {
            $query = DeviceData::where('parameter_type_user_device_id', $type->parameters->id)
                    ->orderByDesc('created_at')
                    ->take(24)
                    ->get();

            $collectedData = DeviceDataResource::collection($query);

            $data['data'][] = [
                'details' => [
                    'name' => $type->name,
                    'unit' => $type->unit,
                    'expected_parameter' => $type->parameters->expected_parameter
                ],
                'min' => $query->min('value'),
                'max' => $query->max('value'),
                'data' => $collectedData,
            ];
        });

        return response()->json($data);
    }
}
