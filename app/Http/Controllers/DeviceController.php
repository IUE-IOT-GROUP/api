<?php

namespace App\Http\Controllers;

use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return DeviceResource::collection(
            Device::all()
        );
    }

    public function store(Request $request): DeviceResource
    {
        $request->validate([
            'name' => ['required'],
        ]);

        $device = Device::create([
            'name' => $request->name,
        ]);

        return new DeviceResource($device);
    }

    public function show(Device $device): DeviceResource
    {
        return new DeviceResource($device);
    }

    public function update(Request $request, Device $device): DeviceResource
    {
        $request->validate([
            'name' => ['required'],
        ]);

        $device->update([
            'name' => $request->name,
        ]);

        return new DeviceResource($device);
    }
}
