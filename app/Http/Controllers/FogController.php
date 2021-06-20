<?php

namespace App\Http\Controllers;

use App\Http\Resources\FogResource;
use App\Models\Fog;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FogController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'place_id' => ['bail', 'required', Rule::exists('places', 'id'), function ($attribute, $value, $fail) use ($request) {
                $exists = Place::where('id', $request->place_id)->where('user_id', $request->user()->id)->exists();
                if (!$exists)
                {
                    $fail('The given place cannot be found');
                }
            }],
            'mac_address' => ['required'],
            'ip_address' => ['required'],
            'port' => ['nullable']
        ], [
            'fog_id.exists' => 'The given fog does not exist.',
        ]);

        $fog = new Fog;
        $fog->name = $request->get('name');
        $fog->place_id = $request->get('place_id');
        $fog->user_id = $request->user()->id;
        $fog->mac_address = $request->get('mac_address');
        $fog->ip_address = $request->get('ip_address');
        $fog->port = $request->get('port', 80);
        $fog->save();

        return new FogResource($fog);
    }


    public function show(Request $request, Fog $fog)
    {
        return new FogResource($fog->load(['place', 'devices']));
    }

    public function update(Request $request, Fog $fog)
    {
        $request->validate([
            'name' => ['required'],
            'place_id' => ['bail', 'required', Rule::exists('places', 'id'), function ($attribute, $value, $fail) use ($request) {
                $exists = Place::where('id', $request->place_id)->where('user_id', $request->user()->id)->exists();
                if (!$exists)
                {
                    $fail('The given place cannot be found');
                }
            }],
            'mac_address' => ['required'],
            'ip_address' => ['required'],
        ], [
            'fog_id.exists' => 'The given fog does not exist.',
        ]);

        $fog->name = $request->name;
        $fog->place_id = $request->place_id;
        $fog->mac_address = $request->mac_address;
        $fog->ip_address = $request->ip_address;
        $fog->save();

        return new FogResource($fog);
    }

    public function destroy(Fog $fog)
    {
        $fog->devices()->delete();
        $fog->delete();

        return $this->success();
    }
}
