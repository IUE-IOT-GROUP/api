<?php

namespace App\Http\Controllers;

use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use App\Models\ParameterType;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return DeviceResource::collection(
            Device::all()
        );
    }

    public function store(Request $request)
    {

    }

    public function show(Device $device)
    {
        //
    }

    public function update(Request $request, Device $device)
    {
        //
    }

    public function destroy(Device $device)
    {
        //
    }
}
