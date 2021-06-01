<?php

namespace App\Http\Controllers;

use App\Http\Resources\Place\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $place = $request->route()->parameters('place');

            if (!empty($place))
            {
                $exists = $request->user()->places->contains($place['place']);

                if (!$exists)
                {
                    return $this->error('Place not found');
                }
            }

            return $next($request);
        });
    }

    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return PlaceResource::collection(
            $request->user()->places()->whereParentId(null)->with('children')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent' => ['nullable', 'exists:places,id'],
        ]);

        if ($request->parent)
        {
            $parentPlace = Place::findOrFail($request->parent);
            $place = $parentPlace->children()->create([
                'name' => $request->name,
            ]);
        }
        else
        {
            $place = $request->user()->places()->create([
                'name' => $request->name,
            ]);
        }

        return new PlaceResource($place);
    }

    public function show(Request $request, Place $place)
    {
        return new PlaceResource($place);
    }

    public function update(Request $request, Place $place)
    {
        $request->validate([
            'name' => 'required',
            'parent' => ['nullable', 'exists:places,id'],
        ]);

        if ($request->parent)
        {
            $parentPlace = Place::findOrFail($request->parent);
            $place = $place->update([
                'name' => $request->name,
                'parent_id' => $parentPlace->id,
            ]);
        }
        else
        {
            $place->update([
                'name' => $request->name,
            ]);
        }

        return new PlaceResource($place);
    }

    public function destroy(Place $place)
    {
        $place->children()->delete();
        $place->devices()->delete();
        $place->delete();


        return $this->success();
    }
}
