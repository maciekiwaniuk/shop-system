<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\SyncCommand\CreateOrder;

use App\Module\Commerce\Application\DTO\CreateOrderDTO;
use App\Common\Application\SyncCommand\SyncCommandInterface;

readonly class CreateOrderCommand implements SyncCommandInterface
{
    public function __construct(
        public CreateOrderDTO $dto,
    ) {
    }
}
