<?php

namespace App\Events;

use App\Models\Place;
use Illuminate\Foundation\Events\Dispatchable;

class PlaceSavedEvent
{
    use Dispatchable;

    public function __construct(public Place $place)
    {
    }
}
