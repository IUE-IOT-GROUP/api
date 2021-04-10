<?php

namespace App\Http\Controllers;

use App\Http\Resources\Place\PlaceCollection;
use App\Http\Resources\Place\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        return new PlaceCollection(
            $request->user()->places()->whereParentId(null)->get()
//            Place::whereParentId(null)->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent' => ['nullable', 'exists:places,id']
        ]);

        if ($request->parent)
        {
            $parentPlace = Place::findOrFail($request->parent);
            $place = $parentPlace->children()->create([
                'name' => $request->name
            ]);
        }
        else
        {
            $place = $request->user()->places()->create([
                'name' => $request->name
            ]);
        }

        return new PlaceResource($place);
    }

    public function show(Place $place)
    {
        return new PlaceResource($place);
    }

    public function update(Request $request, Place $place)
    {
        //
    }

    public function destroy(Place $place)
    {
        //
    }
}
