<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ProductDeletedEvent extends Event
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}
