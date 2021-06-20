<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceDataResource;
use App\Models\DeviceData;
use App\Models\DeviceParameter;
use App\Models\Parameter;
use App\Models\Device;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        abort(501);
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'mac_address' => ['required', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
        ]);

        if (filled($request->mac_address)) {
            // get the device with the given mac address
            $devices = $user->devices()->whereMacAddress($request->mac_address)->get();

            // there might be multiple devices with the same mac address
            $devices->each(function (Device $device) use ($request) {
                // look for the parameters except the mac address
                foreach ($request->except('mac_address') as $parameter => $value) {
                    // find the user parameters related to device with the expected parameter
                    $device_parameters = $device->parameters()->wherePivot('expected_parameter', $parameter);

                    // one device can have distinct parameters
                    $device_parameters = $device_parameters->get()->first();

                    // if parameters are found process them
                    if (!is_null($device_parameters)) {
                        // we need the id of the pivot table
                        $device_parameters = $device_parameters->parameters;

                        $data = new DeviceData();
                        $data->device_id = $device->id;
                        $data->device_parameter_id = $device_parameters->id;
                        $data->value = $value;
                        $data->save();
                    }
                }
            });

            return $this->success();
        }

        return $this->error('An error has occurred.', 400);
    }

    public function show(Device $device): \Illuminate\Http\JsonResponse
    {
        $data = [];

        $device->parameters->each(function (Parameter $type) use (&$data) {
            $latest = DeviceData::where('device_parameter_id', $type->parameters->id)->take(1)->latest()->first();

            $details['details'] = [
                'device_parameter_id' => $type->parameters->id,
                'name' => $type->name,
                'unit' => $type->unit,
                'expected_parameter' => $type->parameters->expected_parameter,
            ];
            $returningGraphData = [];

            if (!is_null($latest)) {
                $yesterday = Carbon::now();

                // if the latest data is before yesterday, load graph data by the latest data
                if ($latest->created_at->diff($yesterday)->days >= 1)
                    $yesterday = $latest->created_at;

                $query = DeviceData::query()
                    ->where('device_parameter_id', $type->parameters->id)
                    ->where('created_at', '>=', $yesterday->subDays())
                    ->orderByDesc('created_at');

                $collectedData = DeviceDataResource::collection($query->get());

                $graphQuery = $query->groupByRaw('date_format(created_at, \'%Y%m%d%H\')')
                    ->select([\DB::raw('AVG(value) AS value'), 'device_id', 'device_parameter_id', \DB::raw('MIN(created_at) as created_at')])
                    ->get();

                $graphData = DeviceDataResource::collection($graphQuery);

                $returningGraphData = [
                    'min_y' => $graphData->min('value'),
                    'max_y' => $graphData->max('value'),
                    'min_x' => $graphQuery->min('created_at')->format('Y-m-d H:i:s'),
                    'max_x' => $collectedData->max('created_at')->format('Y-m-d H:i:s'),
                    'count' => $collectedData->count(),
                    'data' => $collectedData->take(10),
                    'graphData' => $graphData,
                ];
            }

            $data['data'][] = array_merge($details, $returningGraphData);
        });

        return response()->json($data);
    }

    public function showParameter(Request $request, Device $device, DeviceParameter $type): \Illuminate\Http\JsonResponse
    {
        $period = $request->input('period', 'daily');
        ray()->clearScreen();
        $data = [];
        $latest = DeviceData::where('device_parameter_id', $type->id)->take(1)->latest()->first();

        ray($type)->blue();

        $details['details'] = [
            'device_parameter_id' => $type->id,
            'name' => $type->parameter->name,
            'unit' => $type->parameter->unit,
            'expected_parameter' => $type->expected_parameter,
        ];
        $returningGraphData = [];

        if (!is_null($latest)) {
            $yesterday = Carbon::now();

            // if the latest data is before yesterday, load graph data by the latest data
            if ($latest->created_at->diff($yesterday)->days >= 1)
                $yesterday = $latest->created_at;

            $query = DeviceData::query()
                ->where('device_parameter_id', $type->id)
                ->where('device_id', $device->id)
                ->when($period, function (Builder $query, $value) use ($yesterday) {
                    return match ($value) {
                        'weekly' => $query->where('created_at', '>', $yesterday->subDays(7)),
                        'monthly' => $query->where('created_at', '>=', $yesterday->subMonths())->take(5),
                        default => $query->where('created_at', '>=', $yesterday->subDays()),
                    };
                })
                ->orderByDesc('created_at');

            $collectedData = DeviceDataResource::collection($query->get());

            $graphQuery = $query->when($period, function (Builder $query, $value) {
                return match ($value) {
                    'weekly' => $query->groupByRaw('date_format(created_at, \'%Y%m%d\')'),
                    'monthly' => $query->groupByRaw('date_format(created_at, \'%u\')')->take(5),
                    default => $query->groupByRaw('date_format(created_at, \'%Y%m%d%H\')'),
                };
            })
                ->select([\DB::raw('MAX(id) AS id, AVG(value) AS value'), 'device_id', 'device_parameter_id', \DB::raw('MIN(created_at) as created_at')])
                ->tap(function ($query) {
                    ray(\Str::replaceArray('?', $query->getBindings(), $query->toSql()))->blue();
                })
                ->get();

            ray('graph', $graphQuery)->blue();

            $graphData = DeviceDataResource::collection($graphQuery);

            $returningGraphData = [
                'min_y' => $graphData->min('value'),
                'max_y' => $graphData->max('value'),
                'min_x' => $graphQuery->min('created_at')->format('Y-m-d H:i:s'),
                'max_x' => $collectedData->max('created_at')->format('Y-m-d H:i:s'),
                'count' => $graphData->count(),
                'data' => $collectedData->take(10),
                'graphData' => $graphData,
            ];
        }

        $data['data'] = array_merge($details, $returningGraphData);

        return response()->json($data);
    }
}
