<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\ChangeOrderStatus;

use App\Module\Commerce\Application\DTO\ChangeOrderStatusDTO;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use App\Common\Application\Command\CommandInterface;

readonly class ChangeOrderStatusCommand implements CommandInterface
{
    public function __construct(
        public ChangeOrderStatusDTO $dto,
        public string $uuid,
    ) {
    }
}
