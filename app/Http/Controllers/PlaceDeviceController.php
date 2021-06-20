<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceDeviceController extends Controller
{
    public function index(Request $request, Place $place)
    {
        $devices = $request->user()->devices()->where('place_id', $place->id)
            ->with(['deviceType', 'parameters', 'place'])
            ->get();

        return DeviceResource::collection($devices);
    }
}
