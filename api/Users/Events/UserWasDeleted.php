<?php

namespace Api\Users\Events;

use Infrastructure\Events\Event;
use Api\Users\Models\User;

class UserWasDeleted extends Event
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
