<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Port;

interface PaymentsInitializerInterface
{
    public function init(string $orderId, string $userId, float $totalCost): bool;
}
