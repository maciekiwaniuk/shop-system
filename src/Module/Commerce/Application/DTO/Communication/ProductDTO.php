<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\DTO\Communication;

use App\Module\Commerce\Domain\Entity\Product;
use DateTimeImmutable;

final readonly class ProductDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
        public string $slug,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    public static function fromEntity(Product $product): self
    {
        return new self(
            $product->getId(),
            $product->getName(),
            $product->getPrice(),
            $product->getSlug(),
            $product->getCreatedAt(),
            $product->getUpdatedAt(),
        );
    }
}
