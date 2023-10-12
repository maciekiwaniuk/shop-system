<?php

declare(strict_types=1);

namespace App\Module\Order\Domain\Repository;

use App\Module\Order\Domain\Entity\OrderStatusUpdate;

interface OrderStatusUpdateRepositoryInterface
{
    public function save(OrderStatusUpdate $orderStatusUpdate, bool $flush = false): void;
}
