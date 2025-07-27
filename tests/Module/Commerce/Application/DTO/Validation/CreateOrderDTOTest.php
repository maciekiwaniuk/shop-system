<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\DTO\Validation;

use App\Module\Commerce\Application\DTO\Validation\CreateOrderDTO;
use App\Tests\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Group('integration')]
class CreateOrderDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    #[Test]
    public function it_should_pass_validation_when_products_array_is_valid(): void
    {
        $products = [
            [
                'id' => 1,
                'quantity' => 2,
                'pricePerPiece' => 10.50,
            ],
            [
                'id' => 2,
                'quantity' => 1,
                'pricePerPiece' => 25.00,
            ],
        ];
        $dto = new CreateOrderDTO(products: $products);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_products_array_has_single_item(): void
    {
        $products = [
            [
                'id' => 1,
                'quantity' => 1,
                'pricePerPiece' => 15.99,
            ],
        ];
        $dto = new CreateOrderDTO(products: $products);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_products_have_different_data_types(): void
    {
        $products = [
            [
                'id' => '1',
                'quantity' => '2',
                'pricePerPiece' => '10.50',
            ],
        ];
        $dto = new CreateOrderDTO(products: $products);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_pass_validation_when_products_have_extra_keys(): void
    {
        $products = [
            [
                'id' => 1,
                'quantity' => 2,
                'pricePerPiece' => 10.50,
                'name' => 'Extra Product Name',
                'description' => 'Extra description',
            ],
        ];
        $dto = new CreateOrderDTO(products: $products);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function it_should_not_pass_validation_when_products_is_null(): void
    {
        $dto = new CreateOrderDTO(products: null);

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    #[Test]
    public function it_should_not_pass_validation_when_products_is_empty_array(): void
    {
        $dto = new CreateOrderDTO(products: []);

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    #[Test]
    public function it_should_return_correct_error_message_for_null_products(): void
    {
        $dto = new CreateOrderDTO(products: null);

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
        $this->assertEquals('Order must have products.', $errors[0]->getMessage());
    }

    #[Test]
    public function it_should_return_correct_error_message_for_empty_products(): void
    {
        $dto = new CreateOrderDTO(products: []);

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
        $this->assertEquals('Order must have products.', $errors[0]->getMessage());
    }

    #[Test]
    public function it_should_handle_many_products(): void
    {
        $products = [];
        for ($i = 1; $i <= 100; $i++) {
            $products[] = [
                'id' => $i,
                'quantity' => $i,
                'pricePerPiece' => $i * 1.5,
            ];
        }
        $dto = new CreateOrderDTO(products: $products);

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }
}
