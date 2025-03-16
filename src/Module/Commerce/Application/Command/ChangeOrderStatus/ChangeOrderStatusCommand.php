<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\ChangeOrderStatus;

use App\Common\Application\SyncCommand\SyncCommandInterface;
use App\Module\Commerce\Application\DTO\Validation\ChangeOrderStatusDTO;

readonly class ChangeOrderStatusCommand implements SyncCommandInterface
{
    public function __construct(
        public ChangeOrderStatusDTO $dto,
        public string $uuid,
    ) {
    }
}
