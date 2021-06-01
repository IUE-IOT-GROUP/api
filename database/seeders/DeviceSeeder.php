<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\ParameterType;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $p1 = ParameterType::create([
            'name' => 'Temperature',
            'unit' => 'centigrade',
        ]);

        $p2 = ParameterType::create([
            'name' => 'Temperature',
            'unit' => 'kelvin',
        ]);

        $p3 = ParameterType::create([
            'name' => 'Humidity',
            'unit' => 'percent',
        ]);

        $p4 = ParameterType::create([
            'name' => 'Distance',
            'unit' => 'centimeter',
        ]);

        $p5 = ParameterType::create([
            'name' => 'Distance',
            'unit' => 'meter',
        ]);

        $device = Device::create([
            'name' => 'DHT 11',
        ]);
//        $device->parameters()->sync([
//            $p1->id => ['expected_parameter' => 'temperature'],
//            $p2->id => ['expected_parameter' => 'temperature'],
//            $p3->id => ['expected_parameter' => 'humidity']
//        ]);

        $device = Device::create([
            'name' => 'HC-SR04',
        ]);
//        $device->parameters()->sync([
//            $p4->id => ['expected_parameter' => 'distance'],
//            $p5->id => ['expected_parameter' => 'distance']
//        ]);
    }
}
