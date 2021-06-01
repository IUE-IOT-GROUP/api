<?php

namespace App\Http\Resources\Place;

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
            'places' => PlaceResource::collection($this->whenLoaded('children'))
        ];
    }
}

