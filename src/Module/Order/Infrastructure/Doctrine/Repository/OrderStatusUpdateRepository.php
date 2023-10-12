<?php

declare(strict_types=1);

namespace App\Module\Order\Infrastructure\Doctrine\Repository;

use App\Module\Order\Domain\Entity\Order;
use App\Module\Order\Domain\Entity\OrderStatusUpdate;
use App\Module\Order\Domain\Repository\OrderStatusUpdateRepositoryInterface;
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
