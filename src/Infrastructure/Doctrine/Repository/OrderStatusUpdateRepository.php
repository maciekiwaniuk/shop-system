<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Order;
use App\Domain\Entity\OrderStatusUpdate;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Domain\Repository\OrderStatusUpdateRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderStatusUpdate>
 */
class OrderStatusUpdateRepository extends ServiceEntityRepository implements OrderStatusUpdateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(OrderStatusUpdate $orderStatusUpdate, bool $flush = false): void
    {
        $this->getEntityManager()->persist($orderStatusUpdate);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
