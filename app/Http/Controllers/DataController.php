<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceDataResource;
use App\Models\DeviceData;
use App\Models\ParameterType;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        abort(501);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mac_address' => ['required', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
        ]);

        if (filled($request->mac_address))
        {
            // get the device with the given mac address
            $devices = UserDevice::whereMacAddress($request->mac_address)->get();

            // there might be multiple devices with the same mac address
            $devices->each(function (UserDevice $device) use ($request) {
                // look for the parameters except the mac address
                foreach ($request->except('mac_address') as $parameter => $value)
                {
                    // find the user parameters related to device with the expected parameter
                    $device_parameters = $device->parameters()->wherePivot('expected_parameter', $parameter);

                    // one device can have distinct parameters
                    $device_parameters = $device_parameters->get()->first();

                    // if parameters are found process them
                    if (!is_null($device_parameters))
                    {
                        // we need the id of the pivot table
                        $device_parameters = $device_parameters->parameters;

                        $data = new DeviceData();
                        $data->user_device_id = $device->id;
                        $data->parameter_type_user_device_id = $device_parameters->id;
                        $data->value = $value;
                        $data->save();
                    }
                }
            });

            return $this->success();
        }

        return $this->error('An error has occurred.', 400);
    }

    public function show(UserDevice $device)
    {
        $data = [];
        $device->parameters->each(function (ParameterType $type) use (&$data) {
            $data[$type->parameters->expected_parameter] = [
                'details' => [
                    'name' => $type->name,
                    'unit' => $type->unit,
                    'expected_parameter' => $type->parameters->expected_parameter
                ],
                'data' => DeviceDataResource::collection(DeviceData::where('parameter_type_user_device_id', $type->parameters->id)->orderByDesc('created_at')->take(50)->get()),
            ];
        });

        return response()->json($data);
    }
}
