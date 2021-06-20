<?php

namespace App\Http\Controllers;

use App\Http\Requests\Device\StoreRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Models\Parameter;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $devices = $request->user()->devices()->with(['deviceType', 'parameters', 'fog'])->get();

        return DeviceResource::collection($devices);
    }

    public function store(StoreRequest $request): DeviceResource
    {
        $parameters = [];
        foreach ($request->parameters() as $type)
        {
            $p = isset($type['id'])
                ? Parameter::find($type['id'])
                : Parameter::firstOrCreate([
                    'name' => $type['name'],
                    'unit' => $type['unit'],
                ]);

            ray($p->id);

            $parameters[$p->id] = [
                'id' => $type['pivot_id'] ?? '',
                'expected_parameter' => $type['expected_parameter'],
            ];
        }

        ray('parameters', $parameters);

        $device = new Device([
            'id' => $request->id(),
            'name' => $request->name(),
            'mac_address' => $request->macAddress(),
            'ip_address' => $request->ipAddress(),
        ]);

        $device->user()->associate($request->user());
        $device->fog()->associate($request->fog());
        $device->place()->associate($request->place());
        $device->deviceType()->associate($request->deviceType());

        $device->save();
        $device->parameters()->attach($parameters);

        $device->load('parameters');

        $parameters = [];
        foreach ($device->parameters as $parameter)
        {
            $parameters[] = [
                'id' => $parameter->id,
                'name' => $parameter->name,
                'unit' => $parameter->unit,
                'pivot_id' => $parameter->parameters->id,
                'expected_parameter' => $parameter->parameters->expected_parameter
            ];
        }

        $a = array_merge($device->attributesToArray(), ['parameters' => $parameters]);

        ray(json_encode($a));

        return new DeviceResource($device);
    }

    public function show(Device $device): DeviceResource
    {
        $device->load(['parameters', 'deviceType', 'place']);

        return new DeviceResource($device);
    }

    public function update(Request $request, Device $device): DeviceResource
    {
        $request->validate([
            'name' => ['required'],
            'place_id' => ['bail', 'required', Rule::exists('places', 'id'), function ($attribute, $value, $fail) use ($request) {
                $exists = Place::where('id', $request->place_id)->where('user_id', $request->user()->id)->exists();
                if (!$exists)
                {
                    $fail('The given place cannot be found');
                }
            }],
            'mac_address' => ['required'],
            'ip_address' => ['required'],
            'parameters' => ['required', 'array'],
        ]);

        $parameters = [];
        if (!empty($request->parameters))
        {
            foreach ($request->parameters as $parameter => $type)
            {
                $p = Parameter::firstOrCreate([
                    'name' => $type['name'],
                    'unit' => $type['unit'],
                ]);

                $parameters[$p->id] = [
                    'expected_parameter' => $parameter,
                ];
            }
        }

        $device->name = $request->name;
        $device->mac_address = $request->mac_address;
        $device->ip_address = $request->ip_address;
        $device->place_id = $request->place_id;
        $device->save();

        $device->parameters()->sync($parameters);

        return new DeviceResource($device);
    }

    public function destroy(Device $device): \Illuminate\Http\JsonResponse
    {
        $device->data()->delete();
        $device->parameters()->detach();
        $device->delete();

        return response()->json(['success' => true]);
    }
}
