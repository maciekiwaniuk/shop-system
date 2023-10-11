<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product, bool $flush = false): void;

    public function findBySlug(string $slug): Product;

    public function findByUuid(string $uuid): Product;
}
