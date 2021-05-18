<?php

namespace App\Http\Resources\Place;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'places' => PlaceResource::collection($this->children)
        ];
    }
}

