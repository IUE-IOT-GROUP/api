<?php

namespace App\Http\Resources;

use App\Http\Resources\Device\DeviceResource;
use App\Http\Resources\Place\PlaceResource;
use App\Models\UserDeviceParameterType;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UserDevice */
class UserDeviceResource extends JsonResource
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
            'data_count' => $this->data_count,
            'parameters_count' => $this->parameters_count,

            'user_id' => $this->user_id,
            'device_id' => $this->device_id,
            'place_id' => $this->place_id,


            'parameters' => ParameterResource::collection($this->whenLoaded('parameters')),
            'device' => new DeviceResource($this->whenLoaded('device')),
            'place' => new PlaceResource($this->whenLoaded('place')),
        ];
    }
}
