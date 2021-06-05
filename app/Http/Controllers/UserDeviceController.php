<?php

namespace App\Http\Controllers;

use App\Http\Resources\Device\DeviceResource;
use App\Http\Resources\UserDeviceResource;
use App\Models\Device;
use App\Models\ParameterType;
use App\Models\Place;
use App\Models\UserDevice;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserDeviceController extends Controller
{
    public function index(Request $request)
    {
        $devices = $request->user()->devices()->with(['device', 'parameters', 'place'])->get();

        return UserDeviceResource::collection($devices);
    }

    public function store(Request $request)
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
            'device_id' => ['nullable', Rule::unique('user_devices')->where(function (Builder $query) use ($request) {
                return $query->where([
                    ['mac_address', '=', $request->mac_address],
                    ['ip_address', '=', $request->ip_address],
                    ['device_id', '=', $request->device_id],
                    ['place_id', '=', $request->place_id],
                ]);
            })],
            'mac_address' => ['required'],
            'ip_address' => ['required'],
            'parameters' => ['required', 'array'],
        ], [
            'place_id.exists' => 'The given place does not exist.',
            'device_id.unique' => 'The given device is already available within the same place',
        ]);

        $parameters = [];
        foreach ($request->parameters as $parameter => $type)
        {
            $p = ParameterType::firstOrCreate([
                'name' => $type['name'],
                'unit' => $type['unit'],
            ]);

            $parameters[$p->id] = [
                'expected_parameter' => $parameter,
            ];
        }

        if (!is_null($request->device_id))
        {
            $device = Device::find($request->device_id);
        }
        else
        {
            $device = new Device();
            $device->name = $request->name;
            $device->save();
        }

        $userDevice = new UserDevice;
        $userDevice->name = $request->name;
        $userDevice->mac_address = $request->mac_address;
        $userDevice->ip_address = $request->ip_address;
        $userDevice->user_id = $request->user()->id;
        $userDevice->place_id = $request->place_id;
        $userDevice->device_id = $device->id;
        $userDevice->save();

        $userDevice->parameters()->attach($parameters);

        return new UserDeviceResource($userDevice);
    }

    public function show(UserDevice $userDevice): UserDeviceResource
    {
        $userDevice->load(['parameters', 'device', 'data', 'place']);

        return new UserDeviceResource($userDevice);
    }

    public function update(Request $request, UserDevice $userDevice): UserDeviceResource
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
                $p = ParameterType::firstOrCreate([
                    'name' => $type['name'],
                    'unit' => $type['unit'],
                ]);

                $parameters[$p->id] = [
                    'expected_parameter' => $parameter,
                ];
            }
        }

        $userDevice->name = $request->name;
        $userDevice->mac_address = $request->mac_address;
        $userDevice->ip_address = $request->ip_address;
        $userDevice->place_id = $request->place_id;
        $userDevice->save();

        $userDevice->parameters()->sync($parameters);

        return new UserDeviceResource($userDevice);
    }

    public function destroy(UserDevice $userDevice): \Illuminate\Http\JsonResponse
    {
        $userDevice->data()->delete();
        $userDevice->parameters()->detach();
        $userDevice->delete();

        return response()->json(['success' => true]);
    }
}
