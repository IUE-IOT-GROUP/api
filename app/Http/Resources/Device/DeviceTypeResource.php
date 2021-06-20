<?php

namespace App\Http\Resources\Device;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\DeviceType */
class DeviceTypeResource extends JsonResource
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
        ];
    }
}
