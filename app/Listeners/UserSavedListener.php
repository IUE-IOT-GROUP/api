<?php

namespace App\Listeners;

use App\Cloud\Cloud;
use App\Events\UserSavedEvent;
use App\Models\User;

class UserSavedListener
{
    public function __construct()
    {
        //
    }

    public function handle(UserSavedEvent $event)
    {
        $user = $event->user;

        $fields = [];

        foreach (User::FIELDS as $field) {
            $fields[$field] = $user->{$field};
        }

        ray($fields);

        Cloud::post('users/fog', $fields);
    }
}
