<?php

declare(strict_types=1);

namespace App\Module\Order\Application\Command\ChangeOrderStatus;

use App\Module\Order\Application\DTO\ChangeOrderStatusDTO;
use App\Module\Order\Domain\Enum\OrderStatus;
use App\Shared\Application\Command\CommandInterface;

class ChangeOrderStatusCommand implements CommandInterface
{
    public function __construct(
        public readonly ChangeOrderStatusDTO $dto,
        public readonly OrderStatus $uuid
    ) {
    }
}
