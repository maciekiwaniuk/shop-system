<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Repository;

use App\Module\Commerce\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order, bool $flush = false): ?string;

    /**
     * @return array<Order>
     */
    public function getPaginatedByUuid(?string $cursor, int $limit): array;

    public function findByUuid(string $uuid): ?Order;

    public function getReference(string $id): Order;
}
