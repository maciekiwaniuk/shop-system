<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Doctrine\Repository;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function save(Client $client, bool $flush = false): void
    {
        $this->getEntityManager()->persist($client);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findClientByEmail(string $email): ?Client
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getReference(string $id): Client
    {
        return $this->getEntityManager()->getReference(Client::class, $id);
    }
}
