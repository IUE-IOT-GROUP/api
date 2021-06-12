<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceDataResource;
use App\Models\DeviceData;
use App\Models\ParameterType;
use App\Models\DeviceParameter;
use App\Models\UserDevice;
use Carbon\Carbon;
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
            $query = DeviceData::query()
                ->where('device_parameter_id', $type->parameters->id)
                ->where('created_at', '>=', Carbon::now()->subDays())
                ->orderByDesc('created_at')
                ->get();

            $collectedData = DeviceDataResource::collection($query);

            $data['data'][] = [
                'details' => [
                    'device_parameter_id' => $type->parameters->id,
                    'name' => $type->name,
                    'unit' => $type->unit,
                    'expected_parameter' => $type->parameters->expected_parameter
                ],
                'min_y' => $query->min('value'),
                'max_y' => $query->max('value'),
                'min_x' => $query->min('created_at'),
                'max_x' => $query->max('created_at'),
                'count' => $query->count(),
                'data' => $collectedData,
            ];
        });

        return response()->json($data);
    }

    public function showParameter(Request $request, UserDevice $device, DeviceParameter $type)
    {
        $data = [];
//        $device->parameters->each(function (ParameterType $type) use (&$data) {
            $query = DeviceData::query()
                ->where('device_parameter_id', $type->id)
                ->where('user_device_id', $device->id)
                ->when($request->get('period'), function($query, $value) {
                    switch($value) {
                        case 'daily':
                            $period = Carbon::now()->subDays();
                            break;
                        case 'weekly':
                            $period = Carbon::now()->subDays(7);
                            break;
                        case 'monthly':
                            $period = Carbon::now()->subDays(30);
                            break;
                        default:
                            $period = Carbon::now()->subDays();
                    }

                    return $query->where('created_at', '>=', $period);
                })
                ->orderByDesc('created_at')
                ->get();

            $collectedData = DeviceDataResource::collection($query);

            $data['data'] = [
                'details' => [
                    'device_parameter_id' => $type->id,
                    'name' => $type->parameter->name,
                    'unit' => $type->parameter->unit,
                    'expected_parameter' => $type->expected_parameter
                ],
                'min_y' => $query->min('value'),
                'max_y' => $query->max('value'),
                'min_x' => $query->min('created_at'),
                'max_x' => $query->max('created_at'),
                'count' => $query->count(),
                'data' => $collectedData,
            ];
//        });

        return response()->json($data);
    }
}
