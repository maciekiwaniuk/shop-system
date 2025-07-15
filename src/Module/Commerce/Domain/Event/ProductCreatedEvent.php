<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Event;

use App\Module\Commerce\Domain\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

final class ProductCreatedEvent extends Event
{
    public function __construct(
        public readonly Product $product,
    ) {
    }
}
