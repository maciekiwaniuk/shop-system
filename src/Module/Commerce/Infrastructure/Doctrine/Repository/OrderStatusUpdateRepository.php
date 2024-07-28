<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Doctrine\Repository;

use App\Module\Commerce\Domain\Entity\OrderStatusUpdate;
use App\Module\Commerce\Domain\Repository\OrderStatusUpdateRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderStatusUpdate>
 */
class OrderStatusUpdateRepository extends ServiceEntityRepository implements OrderStatusUpdateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderStatusUpdate::class);
    }

    public function save(OrderStatusUpdate $orderStatusUpdate, bool $flush = false): void
    {
        $this->getEntityManager()->persist($orderStatusUpdate);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
