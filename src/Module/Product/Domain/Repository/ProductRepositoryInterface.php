<?php

declare(strict_types=1);

namespace App\Module\Product\Domain\Repository;

use App\Module\Product\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product, bool $flush = false): void;

    public function getAll(): array;

    public function findBySlug(string $slug): ?Product;

    public function findById(int $id): ?Product;
}
