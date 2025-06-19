<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Port;

interface ClientFinderInterface
{
    public function findClientIdByEmail(string $email): ?string;
}
