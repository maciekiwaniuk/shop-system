<?php

declare(strict_types=1);

namespace App\Module\Order\Domain\Repository;

use App\Module\Order\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order, bool $flush = false): void;

    public function findByUuid(string $uuid): Order;
}
