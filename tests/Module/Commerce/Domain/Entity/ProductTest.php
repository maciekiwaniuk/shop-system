<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Entity\Product;
use App\Tests\AbstractUnitTestCase;

class ProductTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_should_create_a_product_with_valid_initial_values(): void
    {
        $product = new Product(
            'exampleName',
            3.21,
        );

        $this->assertEquals('exampleName', $product->getName());
        $this->assertEquals(3.21, $product->getPrice());
        $this->assertNotNull($product->getSlug());
        $this->assertNotNull($product->getCreatedAt());
        $this->assertNotNull($product->getUpdatedAt());
        $this->assertNull($product->getDeletedAt());
    }
}
