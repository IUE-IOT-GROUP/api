<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceResource;
use App\Http\Resources\FogResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceFogController extends Controller
{
    public function index(Request $request, Place $place)
    {
        $devices = $request->user()->fogs()->where('place_id', $place->id)
            ->get();

        return FogResource::collection($devices);
    }
}
