<?php

namespace Database\Seeders;

use App\Models\DeviceType;
use App\Models\Parameter;
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
        $p1 = Parameter::create([
            'name' => 'Temperature',
            'unit' => 'centigrade',
        ]);

        $p2 = Parameter::create([
            'name' => 'Temperature',
            'unit' => 'kelvin',
        ]);

        $p3 = Parameter::create([
            'name' => 'Humidity',
            'unit' => 'percent',
        ]);

        $p4 = Parameter::create([
            'name' => 'Distance',
            'unit' => 'centimeter',
        ]);

        $p5 = Parameter::create([
            'name' => 'Distance',
            'unit' => 'meter',
        ]);

        $device = DeviceType::create([
            'name' => 'DHT 11',
        ]);
//        $device->parameters()->sync([
//            $p1->id => ['expected_parameter' => 'temperature'],
//            $p2->id => ['expected_parameter' => 'temperature'],
//            $p3->id => ['expected_parameter' => 'humidity']
//        ]);

        $device = DeviceType::create([
            'name' => 'HC-SR04',
        ]);
//        $device->parameters()->sync([
//            $p4->id => ['expected_parameter' => 'distance'],
//            $p5->id => ['expected_parameter' => 'distance']
//        ]);
    }
}
