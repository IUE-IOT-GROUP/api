<?php

namespace App\Http\Controllers;

use App\Models\DeviceData;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
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
        }
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
