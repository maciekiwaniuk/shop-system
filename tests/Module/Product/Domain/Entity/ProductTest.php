<?php

declare(strict_types=1);

namespace App\Tests\Module\Product\Domain\Entity;

use App\Module\Product\Domain\Entity\Product;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

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

    public function testGenerateSlug(): void
    {
        $product = new Product(
            'exampleName',
            3.21
        );

        $slug = (new AsciiSlugger())->slug($product->getName()) . '-' . substr($product->getId(), 0, 8);

        $this->assertEquals($slug, $product->getSlug());
    }
}
