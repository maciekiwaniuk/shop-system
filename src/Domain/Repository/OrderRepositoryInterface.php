<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order, bool $flush = false): void;

    public function remove(Order $order, bool $flush = false): void;

    public function findByUuid(string $uuid): Order;
}
