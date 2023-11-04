<?php

declare(strict_types=1);

namespace App\Module\Product\Infrastructure\Doctrine\Repository;

use App\Module\Product\Domain\Entity\Product;
use App\Module\Product\Domain\Repository\ProductRepositoryInterface;
use DateTimeImmutable;
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

    public function findBySlug(string $slug): Product
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.slug = :slug')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByUuid(string $uuid): ?Product
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.id = :id')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('id', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function softDelete(Product $product): bool
    {
        return (bool) $this->createQueryBuilder('p')
            ->update()
            ->set('p.deletedAt', ':deletedAt')
            ->where('p.id = :id')
            ->setParameter('deletedAt', new DateTimeImmutable())
            ->setParameter('id', $product->getId())
            ->getQuery()
            ->execute();
    }

    public function update(
        Product $product,
        string $name,
        float $price
    ): bool {
        return (bool) $this->createQueryBuilder('p')
            ->update()
            ->set('p.name', ':name')
            ->set('p.price', ':price')
            ->where('p.id = :id')
            ->setParameter('name', $name)
            ->setParameter('price', $price)
            ->setParameter('id', $product->getId())
            ->getQuery()
            ->execute();
    }
}
