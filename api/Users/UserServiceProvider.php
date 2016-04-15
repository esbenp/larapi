<?php

namespace Api\Users;

use Infrastructure\Events\EventServiceProvider;
use Api\Users\Events\UserWasCreated;
use Api\Users\Events\UserWasDeleted;
use Api\Users\Events\UserWasUpdated;

class UserServiceProvider extends EventServiceProvider
{
    protected $listen = [
        UserWasCreated::class => [
            // listeners for when a user is created
        ],
        UserWasDeleted::class => [
            // listeners for when a user is deleted
        ],
        UserWasUpdated::class => [
            // listeners for when a user is updated
        ]
    ];
}
