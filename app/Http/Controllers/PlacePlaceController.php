<?php

namespace App\Http\Controllers;

use App\Http\Resources\Place\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlacePlaceController extends Controller
{
    public function index(Request $request, Place $place)
    {
        $places = $request->user()->places()->where('parent_id', $place->id)->with(['children', 'children.children'])->withCount('children')->get();

        if ($places->count() === 0) {
            return new PlaceResource($place);
        }

        return PlaceResource::collection($places);
    }
}
