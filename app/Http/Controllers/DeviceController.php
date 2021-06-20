<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceResource;
use App\Models\DeviceType;
use App\Models\Fog;
use App\Models\Parameter;
use App\Models\Place;
use App\Models\Device;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $devices = $request->user()->devices()->with(['deviceType', 'parameters', 'fog'])->get();

        return DeviceResource::collection($devices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'fog_id' => ['bail', 'required', Rule::exists('fogs', 'id'), function ($attribute, $value, $fail) use ($request) {
                $exists = Fog::where('id', $request->fog_id)->where('user_id', $request->user()->id)->exists();
                if (!$exists)
                {
                    $fail('The given place cannot be found');
                }
            }],
            'place_id' => ['bail', 'required', Rule::exists('places', 'id'), function ($attribute, $value, $fail) use ($request) {
                $exists = Place::where('id', $request->place_id)->where('user_id', $request->user()->id)->exists();
                if (!$exists)
                {
                    $fail('The given place cannot be found');
                }
            }],
            'device_type_id' => ['nullable', Rule::unique('devices')->where(function (Builder $query) use ($request) {
                return $query->where([
                    ['mac_address', '=', $request->mac_address],
                    ['ip_address', '=', $request->ip_address],
                    ['device_type_id', '=', $request->device_type_id],
                    ['fog_id', '=', $request->fog_id],
                ]);
            })],
            'mac_address' => ['required'],
            'ip_address' => ['required'],
            'parameters' => ['required', 'array'],
        ], [
            'fog_id.exists' => 'The given place does not exist.',
            'device_id.unique' => 'The given device is already available within the same place',
        ]);

        $parameters = [];
        foreach ($request->parameters as $type)
        {
            $p = Parameter::firstOrCreate([
                'name' => $type['name'],
                'unit' => $type['unit'],
            ]);

            $parameters[$p->id] = [
                'expected_parameter' => $type['expected_parameter'],
            ];
        }

        if (!is_null($request->device_type_id))
        {
            $device = DeviceType::find($request->device_type_id);
        }
        else
        {
            $device = new DeviceType();
            $device->name = $request->name;
            $device->save();
        }

        $userDevice = new Device;
        $userDevice->name = $request->name;
        $userDevice->mac_address = $request->mac_address;
        $userDevice->ip_address = $request->ip_address;
        $userDevice->user_id = $request->user()->id;
        $userDevice->fog_id = $request->fog_id;
        $userDevice->place_id = $request->place_id;
        $userDevice->device_type_id = $device->id;
        $userDevice->save();

        $userDevice->parameters()->attach($parameters);

        return new DeviceResource($userDevice);
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
