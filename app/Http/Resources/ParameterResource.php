<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UserDeviceParameterType */
class ParameterResource extends JsonResource
{
    public function toArray($request)
    {
        ray($this)->blue();
        $return = [];
        $return[$this->parameters->expected_parameter] = [
            'name' => $this->name,
            'unit' => $this->unit
        ];

        return $return;
    }
}
