<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Doctrine\Repository;

use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $order, bool $flush = false): void
    {
        $this->getEntityManager()->persist($order);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<Order>
     */
    public function getPaginatedByUuid(?string $cursor = null, int $limit = 10): array
    {
        $query = $this->createQueryBuilder('o')
            ->select('o');

        if (isset($cursor)) {
            $query->where('o.id > :cursor')
                ->setParameter('cursor', $cursor);
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function findByUuid(string $uuid): ?Order
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.id = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getReference(string $id): Order
    {
        return $this->getEntityManager()->getReference(Order::class, $id);
    }
}
