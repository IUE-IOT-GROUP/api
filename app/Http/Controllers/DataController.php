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

    public function show(UserDevice $device): \Illuminate\Http\JsonResponse
    {
        $data = [];
        $device->parameters->each(function (ParameterType $type) use (&$data) {
            $query = DeviceData::query()
                ->where('device_parameter_id', $type->parameters->id)
                ->where('created_at', '>=', Carbon::now()->subDays())
                ->orderByDesc('created_at');

            $collectedData = DeviceDataResource::collection($query->get());

            $graphQuery = $query->groupByRaw('date_format(created_at, \'%Y%m%d%H\')')
                ->select([\DB::raw('AVG(value) AS value'), 'user_device_id', 'device_parameter_id', \DB::raw('MIN(created_at) as created_at')])
                ->get();

            $graphData = DeviceDataResource::collection($graphQuery);

            $data['data'][] = [
                'details' => [
                    'device_parameter_id' => $type->parameters->id,
                    'name' => $type->name,
                    'unit' => $type->unit,
                    'expected_parameter' => $type->parameters->expected_parameter,
                ],
                'min_y' => $graphData->min('value'),
                'max_y' => $graphData->max('value'),
                'min_x' => $graphQuery->min('created_at')->format('Y-m-d H:i:s'),
                'max_x' => $collectedData->max('created_at')->format('Y-m-d H:i:s'),
                'count' => $collectedData->count(),
                'data' => $collectedData->take(10),
                'graphData' => $graphData,
            ];
        });

        return response()->json($data);
    }

    public function showParameter(Request $request, UserDevice $device, DeviceParameter $type): \Illuminate\Http\JsonResponse
    {
        $data = [];
        $query = DeviceData::query()
            ->where('device_parameter_id', $type->id)
            ->where('user_device_id', $device->id)
            ->when($request->get('period'), function ($query, $value) {
                return match ($value)
                {
                    'weekly' => $query->where('created_at', '>', Carbon::now()->subDays(7)),
                    'monthly' => $query->where('created_at', '>=', Carbon::now()->subMonths()),
                    default => $query->where('created_at', '>=', Carbon::now()->subDays()),
                };
            })
            ->orderByDesc('created_at');

        $collectedData = DeviceDataResource::collection($query->get());

        $graphQuery = $query->when($request->get('period'), function ($query, $value) {
            return match ($value)
            {
                'weekly' => $query->groupByRaw('date_format(created_at, \'%Y%m%d\')'),
                'monthly' => $query->groupByRaw('date_format(created_at, \'%u\')'),
                default => $query->groupByRaw('date_format(created_at, \'%Y%m%d%H\')'),
            };
        })
            ->select([\DB::raw('MAX(id) AS id, AVG(value) AS value'), 'user_device_id', 'device_parameter_id', \DB::raw('MIN(created_at) as created_at')])
            ->tap(function ($query) {
//                ray(\Str::replaceArray('?', $query->getBindings(), $query->toSql()));
            })
            ->get();

        $graphData = DeviceDataResource::collection($graphQuery);

        $data['data'] = [
            'details' => [
                'device_parameter_id' => $type->id,
                'name' => $type->parameter->name,
                'unit' => $type->parameter->unit,
                'expected_parameter' => $type->expected_parameter,
            ],
            'min_y' => $graphData->min('value'),
            'max_y' => $graphData->max('value'),
            'min_x' => $graphQuery->min('created_at')->format('Y-m-d H:i:s'),
            'max_x' => $collectedData->max('created_at')->format('Y-m-d H:i:s'),
            'count' => $graphData->count(),
            'data' => $collectedData->take(10),
            'graphData' => $graphData,
        ];

        return response()->json($data);
    }
}
