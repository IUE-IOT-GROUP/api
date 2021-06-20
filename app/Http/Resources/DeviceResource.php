<?php

namespace App\Http\Resources;

use App\Http\Resources\Device\DeviceTypeResource;
use App\Http\Resources\Place\PlaceResource;
use App\Models\DeviceParameter;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Device */
class DeviceResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mac_address' => $this->mac_address,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parameters_count' => $this->parameters_count,

            'user_id' => $this->user_id,
            'device_type_id' => $this->device_type_id,
            'fog_id' => $this->fog_id,


            'parameters' => ParameterResource::collection($this->whenLoaded('parameters')),
            'device_type' => new DeviceTypeResource($this->whenLoaded('deviceType')),
            'place' => new PlaceResource($this->whenLoaded('place')),
        ];
    }
}
