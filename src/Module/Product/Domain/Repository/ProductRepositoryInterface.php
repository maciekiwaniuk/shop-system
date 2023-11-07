<?php

declare(strict_types=1);

namespace App\Module\Product\Domain\Repository;

use App\Module\Product\Domain\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;

interface ProductRepositoryInterface
{
    public function save(Product $product, bool $flush = false): void;

    public function getAll(): ArrayCollection;

    public function findBySlug(string $slug): ?Product;

    public function findByUuid(string $uuid): ?Product;
}
