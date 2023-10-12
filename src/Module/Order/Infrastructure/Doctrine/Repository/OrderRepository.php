<?php

declare(strict_types=1);

namespace App\Module\Order\Infrastructure\Doctrine\Repository;

use App\Module\Order\Domain\Entity\Order;
use App\Module\Order\Domain\Repository\OrderRepositoryInterface;
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

    public function findByUuid(string $uuid): Order
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.id = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getSingleResult();
    }
}
