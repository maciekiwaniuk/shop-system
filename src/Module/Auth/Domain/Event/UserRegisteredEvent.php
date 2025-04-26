<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Module\Auth\Domain\ValueObject\UserRegistrationDetails;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisteredEvent extends Event
{
    public function __construct(
        public readonly UserRegistrationDetails $dto,
    ) {
    }
}