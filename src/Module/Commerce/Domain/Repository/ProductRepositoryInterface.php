<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Repository;

use App\Module\Commerce\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product, bool $flush = false): ?int;

    /**
     * @return array<Product>
     */
    public function getPaginatedById(int $offset, int $limit): array;

    public function findBySlug(string $slug): ?Product;

    public function findById(int $id): ?Product;

    public function softDelete(Product $product): bool;

    public function getReference(int $id): Product;
}
