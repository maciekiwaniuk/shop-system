<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Repository;

use App\Module\Commerce\Domain\Entity\OrderStatusUpdate;

interface OrderStatusUpdateRepositoryInterface
{
    public function save(OrderStatusUpdate $orderStatusUpdate, bool $flush = false): void;
}
