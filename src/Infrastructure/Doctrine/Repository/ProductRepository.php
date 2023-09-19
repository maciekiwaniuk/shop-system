<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->persist($product);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->remove($product);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
