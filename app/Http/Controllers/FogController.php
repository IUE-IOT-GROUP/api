<?php

namespace App\Http\Controllers;

use App\Cloud\Cloud;
use App\Http\Requests\Fog\StoreRequest;
use App\Http\Requests\Fog\UpdateRequest;
use App\Http\Resources\FogResource;
use App\Models\Fog;
use Illuminate\Http\Request;

class FogController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreRequest $request): FogResource
    {
        ray($request)->blue();
        $fog = new Fog([
            'id' => $request->id(),
            'name' => $request->name(),
            'mac_address' => $request->macAddress(),
            'ip_address' => $request->ipAddress(),
            'port' => $request->port(),
        ]);

        $fog->place()->associate($request->place());
        $fog->user()->associate($request->user());

        $fog->save();

        if (isFog())
        {
            Cloud::post('fogs', $fog->attributesToArray());
        }

        return new FogResource($fog);
    }


    public function show(Request $request, Fog $fog): FogResource
    {
        return new FogResource($fog->load(['place', 'devices']));
    }

    public function update(UpdateRequest $request, Fog $fog): FogResource
    {
        $fog->update([
            'name' => $request->name(),
            'mac_address' => $request->macAddress(),
            'ip_address' => $request->ipAddress(),
            'port' => $request->port(),
        ]);

        $fog->place()->associate($request->place());

        $fog->save();

        if (isFog())
        {
            Cloud::put('fogs/' . $fog->id, $fog->attributesToArray());
        }

        return new FogResource($fog);
    }

    public function destroy(Fog $fog): \Illuminate\Http\JsonResponse
    {
        $fog->devices()->delete();
        $fog->delete();

        if (isFog())
        {
            Cloud::delete('fogs' . $fog->id);
        }

        return $this->success();
    }
}
