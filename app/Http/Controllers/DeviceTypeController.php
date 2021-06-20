<?php

namespace App\Http\Controllers;

use App\Http\Resources\Device\DeviceTypeResource;
use App\Models\DeviceType;
use Illuminate\Http\Request;

class DeviceTypeController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return DeviceTypeResource::collection(
            DeviceType::all()
        );
    }

    public function store(Request $request): DeviceTypeResource
    {
        $request->validate([
            'name' => ['required'],
        ]);

        $device = DeviceType::create([
            'name' => $request->name,
        ]);

        return new DeviceTypeResource($device);
    }

    public function show(DeviceType $device): DeviceTypeResource
    {
        return new DeviceTypeResource($device);
    }

    public function update(Request $request, DeviceType $device): DeviceTypeResource
    {
        $request->validate([
            'name' => ['required'],
        ]);

        $device->update([
            'name' => $request->name,
        ]);

        return new DeviceTypeResource($device);
    }
}
