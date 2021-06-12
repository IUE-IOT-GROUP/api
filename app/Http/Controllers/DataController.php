<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceDataResource;
use App\Models\DeviceData;
use App\Models\DeviceParameter;
use App\Models\ParameterType;
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
                ->orderByDesc('created_at');

            $collectedData = DeviceDataResource::collection($query->get());

            $graphData = DeviceDataResource::collection(
                $query->groupByRaw('HOUR(created_at)')
                    ->select([\DB::raw('AVG(value) AS value'), 'user_device_id', 'device_parameter_id', 'created_at'])
                    ->get()
            );

            $data['data'][] = [
                'details' => [
                    'device_parameter_id' => $type->parameters->id,
                    'name' => $type->name,
                    'unit' => $type->unit,
                    'expected_parameter' => $type->parameters->expected_parameter,
                ],
                'min_y' => $graphData->min('value'),
                'max_y' => $graphData->max('value'),
                'min_x' => $graphData->min('created_at'),
                'max_x' => $graphData->max('created_at'),
                'count' => $collectedData->count(),
                'data' => $collectedData->take(10),
                'graphData' => $graphData,
            ];
        });

        return response()->json($data);
    }

    public function showParameter(Request $request, UserDevice $device, DeviceParameter $type)
    {
        $data = [];
        $query = DeviceData::query()
            ->where('device_parameter_id', $type->id)
            ->where('user_device_id', $device->id)
            ->when($request->get('period'), function ($query, $value) {
                switch ($value)
                {
                    case 'daily':
                        $period = Carbon::now()->subDays();
                        $query = $query->where('created_at', '>=', $period);
                        break;
                    case 'weekly':
                        $period = Carbon::now()->subDays(6);
                        $query = $query->where('created_at', '>', $period);
                        break;
                    case 'monthly':
                        $period = Carbon::now()->subDays(30);
                        $query = $query->where('created_at', '>=', $period);
                        break;
                    default:
                        $period = Carbon::now()->subDays();
                        $query = $query->where('created_at', '>=', $period);
                }

                return $query;
            })
            ->orderByDesc('created_at');

        $collectedData = DeviceDataResource::collection($query->get());

        $graphData = DeviceDataResource::collection(
            $query->when($request->get('period'), function ($query, $value) {
                switch ($value)
                {
                    case 'daily':
                        $query = $query->groupByRaw('HOUR(created_at)');
                        break;
                    case 'weekly':
                        $query = $query->groupByRaw('DAY(created_at)');
                        break;
                    case 'monthly':
                        $query = $query->groupByRaw('WEEK(created_at)');
                        break;
                    default:
                        $query = $query->groupByRaw('HOUR(created_at)');
                }

                return $query;
            })->select([\DB::raw('AVG(value) AS value'), 'user_device_id', 'device_parameter_id', 'created_at'])
                ->get()
        );

        $data['data'] = [
            'details' => [
                'device_parameter_id' => $type->id,
                'name' => $type->parameter->name,
                'unit' => $type->parameter->unit,
                'expected_parameter' => $type->expected_parameter,
            ],
            'min_y' => $graphData->min('value'),
            'max_y' => $graphData->max('value'),
            'min_x' => $graphData->min('created_at'),
            'max_x' => $graphData->max('created_at'),
            'count' => $graphData->count(),
            'data' => $collectedData->take(10),
            'graphData' => $graphData,
        ];

        return response()->json($data);
    }
}
