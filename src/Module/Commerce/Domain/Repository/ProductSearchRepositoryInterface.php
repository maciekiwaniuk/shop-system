<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Repository;

use App\Module\Commerce\Domain\Entity\Product;

interface ProductSearchRepositoryInterface
{
    public function indexProduct(Product $product): void;

    public function removeProduct(int $id): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchByPhrase(string $phrase): array;

    public function createIndex(): void;
}
