<?php

namespace App\Http\Controllers;

use App\Cloud\Cloud;
use App\Http\Requests\Place\StoreRequest;
use App\Http\Requests\Place\UpdateRequest;
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
        $places = $request->user()->places()->whereParentId(null);

        if ($request->filled('with'))
        {
            $with = explode(',', $request->with);
            $places = $places->with($with);
        }

        return PlaceResource::collection($places->get());
    }

    public function store(StoreRequest $request)
    {
        $place = new Place([
            'id' => $request->id(),
            'name' => $request->name(),
        ]);
        $place->parent()->associate($request->parent());
        $place->user()->associate($request->user());

        $place->save();

        if (isFog())
        {
            ray($place->attributesToArray());
            Cloud::post('places', $place->attributesToArray());
        }

        return new PlaceResource($place);
    }

    public function show(Request $request, Place $place)
    {
        $place->load(['children', 'fogs', 'devices', 'children.children', 'children.fogs', 'children.devices']);

        return new PlaceResource($place);
    }

    public function update(UpdateRequest $request, Place $place)
    {
        $place->update([
            'name' => $request->name(),
        ]);

        if (isFog())
        {
            Cloud::put('places/' . $place->id, $place->attributesToArray());
        }

        return new PlaceResource($place);
    }

    public function destroy(Place $place)
    {
        $place->children()->delete();
        $place->devices()->delete();
        $place->delete();

        if (isFog())
        {
            Cloud::delete('places/' . $place->id);
        }

        return $this->success();
    }

    public function fogs()
    {
        $places = Place::has('fogs')->with('fogs')->get();

        return PlaceResource::collection($places);
    }
}
