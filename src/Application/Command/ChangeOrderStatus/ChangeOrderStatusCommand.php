<?php

declare(strict_types=1);

namespace App\Application\Command\ChangeOrderStatus;

use App\Application\Command\CommandInterface;
use App\Application\DTO\Order\ChangeOrderStatusDTO;
use App\Domain\Enum\OrderStatus;

class ChangeOrderStatusCommand implements CommandInterface
{
    public function __construct(
        public readonly ChangeOrderStatusDTO $dto,
        public readonly OrderStatus $uuid
    ) {
    }
}
