<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Parameter */
class ParameterResource extends JsonResource
{
    public function toArray($request)
    {
        $return = [];
        $return[$this->parameters->expected_parameter] = [
            'name' => $this->name,
            'unit' => $this->unit
        ];

        return $return;
    }
}
