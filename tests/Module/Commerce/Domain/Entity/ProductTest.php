<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Domain\Entity;

use App\Module\Commerce\Domain\Entity\Product;
use App\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('unit')]
class ProductTest extends AbstractUnitTestCase
{
    #[Test]
    public function it_should_create_a_product_with_valid_initial_values(): void
    {
        $product = new Product(
            'Example Product Name',
            3.21,
        );

        $this->assertEquals('Example Product Name', $product->getName());
        $this->assertEquals(3.21, $product->getPrice());
        $this->assertNotNull($product->getSlug());
        $this->assertNotNull($product->getCreatedAt());
        $this->assertNotNull($product->getUpdatedAt());
        $this->assertNull($product->getDeletedAt());
    }

    #[Test]
    public function it_should_generate_slug_from_name(): void
    {
        $product = new Product('My super great product', 10.00);

        $this->assertStringContainsString('my-super-great-product', $product->getSlug());
    }
}
