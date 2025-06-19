<?php

namespace App\Module\Commerce\Infrastructure\Adapter;

use App\Module\Auth\Application\Port\ClientFinderInterface;
use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;

readonly class AuthClientFinderAdapter implements ClientFinderInterface
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
    ) {
    }

    public function findClientIdByEmail(string $email): ?string
    {
        $client = $this->clientRepository->findClientByEmail($email);
        return $client?->getId();
    }
}