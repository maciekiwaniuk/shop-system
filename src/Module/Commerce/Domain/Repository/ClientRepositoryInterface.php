<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Repository;

use App\Module\Commerce\Domain\Entity\Client;

interface ClientRepositoryInterface
{
    public function save(Client $client, bool $flush = false): void;

    public function findClientByEmail(string $email): ?Client;
}
