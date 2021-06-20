<?php

namespace App\Http\Resources\Place;

use App\Http\Resources\Device\DeviceTypeResource;
use App\Http\Resources\FogResource;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Place */
class PlaceResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'places' => PlaceResource::collection($this->whenLoaded('children')),
            'fogs' => FogResource::collection($this->whenLoaded('fogs')),
            'devices' => DeviceTypeResource::collection($this->whenLoaded('devices')),
        ];
    }
}

