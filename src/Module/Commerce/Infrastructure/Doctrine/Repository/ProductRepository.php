<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Doctrine\Repository;

use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Module\Commerce\Domain\Entity\Product;
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

    public function save(Product $product, bool $flush = false): ?int
    {
        $this->getEntityManager()->persist($product);

        if ($flush) {
            $this->getEntityManager()->flush();
            return $product->getId();
        }
        return null;
    }

    /**
     * @return array<Product>
     */
    public function getPaginatedById(int $offset = 0, int $limit = 10): array
    {
        $limit = min($limit, 20);
        $offset = max(0, $offset);

        return $this->createQueryBuilder('p')
            ->select('p')
            ->andWhere('p.deletedAt IS NULL')
            ->orderBy('p.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.slug = :slug')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findById(int $id): ?Product
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.id = :id')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('id', $id)
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

    public function getReference(int $id): Product
    {
        return $this->getEntityManager()->getReference(Product::class, $id);
    }
}
