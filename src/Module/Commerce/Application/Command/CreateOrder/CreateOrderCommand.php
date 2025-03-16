<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateOrder;

use App\Common\Application\SyncCommand\SyncCommandInterface;
use App\Module\Commerce\Application\DTO\Validation\CreateOrderDTO;

readonly class CreateOrderCommand implements SyncCommandInterface
{
    public function __construct(
        public CreateOrderDTO $dto,
    ) {
    }
}
