<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Order;
use App\Domain\Repository\OrderRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function remove(Order $order, bool $flush = false): void
    {
        $this->getEntityManager()->remove($order);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
