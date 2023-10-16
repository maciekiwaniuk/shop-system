<?php

declare(strict_types=1);

namespace App\Tests\Module\Product\Domain\Entity;

use App\Module\Product\Domain\Entity\Product;
use App\Tests\AbstractUnitTestCase;

class ProductTest extends AbstractUnitTestCase
{
    public function testCreate(): void
    {
        $product = new Product(
            'exampleName',
            3.21
        );

        $this->assertEquals('exampleName', $product->getName());
        $this->assertEquals(3.21, $product->getPrice());
        $this->assertNotNull($product->getSlug());
        $this->assertNotNull($product->getId());
        $this->assertNotNull($product->getCreatedAt());
        $this->assertNotNull($product->getUpdatedAt());
        $this->assertNull($product->getDeletedAt());
    }
}
