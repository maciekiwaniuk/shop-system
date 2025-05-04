<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Event;

use App\Module\Commerce\Application\DTO\Communication\ProductDTO;
use Symfony\Contracts\EventDispatcher\Event;

final class ProductUpdatedEvent extends Event
{
    public function __construct(
        public readonly ProductDTO $dto,
    ) {
    }
}
