<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\OrderStatusUpdate;

interface OrderStatusUpdateRepositoryInterface
{
    public function save(OrderStatusUpdate $orderStatusUpdate, bool $flush = false): void;
}
