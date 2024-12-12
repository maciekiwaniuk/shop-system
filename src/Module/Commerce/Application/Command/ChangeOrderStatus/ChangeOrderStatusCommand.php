<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\SyncCommand\ChangeOrderStatus;

use App\Module\Commerce\Application\DTO\ChangeOrderStatusDTO;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use App\Common\Application\SyncCommand\SyncCommandInterface;

readonly class ChangeOrderStatusCommand implements SyncCommandInterface
{
    public function __construct(
        public ChangeOrderStatusDTO $dto,
        public string $uuid,
    ) {
    }
}
