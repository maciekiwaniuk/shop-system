<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Module\Auth\Application\DTO\Communication\UserRegisteredDTO;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisteredEvent extends Event
{
    public function __construct(
        public readonly UserRegisteredDTO $dto,
    ) {
    }
}