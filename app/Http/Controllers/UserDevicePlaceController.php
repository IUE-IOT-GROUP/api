<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserDeviceResource;
use App\Models\Place;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class UserDevicePlaceController extends Controller
{
    public function index(Request $request, Place $place)
    {
        $devices = $request->user()->devices()->where('place_id', $place->id)
            ->with(['device'])
            ->get();

        return UserDeviceResource::collection($devices);
    }

    public function store()
    {

    }

    public function show()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
