<?php

namespace App\Http\Resources;

use App\Http\Resources\Place\PlaceResource;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Fog */
class FogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mac_address' => $this->mac_address,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at,

            'devices' => DeviceResource::collection($this->whenLoaded('devices')),
            'place' => new PlaceResource($this->whenLoaded('place')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
