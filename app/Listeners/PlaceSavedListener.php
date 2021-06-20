<?php

namespace App\Listeners;

use App\Cloud\Cloud;
use App\Events\PlaceSavedEvent;
use App\Models\Place;

class PlaceSavedListener
{
    public function __construct()
    {
        //
    }

    public function handle(PlaceSavedEvent $event)
    {
        $place = $event->place;

        $fields = [];

        foreach (Place::FIELDS as $field)
        {
            $fields[$field] = $place->{$field};
        }

        Cloud::post('places', $fields);
    }
}
