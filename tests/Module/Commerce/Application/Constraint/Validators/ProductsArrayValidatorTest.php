<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\Constraint\Validators;

use App\Module\Commerce\Application\Constraint\ProductsArray;
use App\Module\Commerce\Application\Constraint\Validators\ProductsArrayValidator;
use App\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[Group('unit')]
class ProductsArrayValidatorTest extends AbstractUnitTestCase
{
    private ProductsArrayValidator $validator;
    private ProductsArray $constraint;
    private ExecutionContextInterface $context;
    private ConstraintViolationBuilderInterface $violationBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ProductsArrayValidator();
        $this->constraint = new ProductsArray();
        $this->violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->violationBuilder->method('addViolation')->willReturnSelf();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->context->method('buildViolation')->willReturn($this->violationBuilder);
        $this->validator->initialize($this->context);
    }

    #[Test]
    public function it_should_validate_empty_array(): void
    {
        $value = [];
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($value, $this->constraint);
    }

    #[Test]
    public function it_should_validate_array_with_valid_product(): void
    {
        $value = [
            [
                'id' => 'product-1',
                'quantity' => 2,
                'pricePerPiece' => 10.50,
            ],
        ];
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($value, $this->constraint);
    }

    #[Test]
    public function it_should_validate_array_with_multiple_valid_products(): void
    {
        $value = [
            [
                'id' => 'product-1',
                'quantity' => 2,
                'pricePerPiece' => 10.50,
            ],
            [
                'id' => 'product-2',
                'quantity' => 1,
                'pricePerPiece' => 25.00,
            ],
            [
                'id' => 'product-3',
                'quantity' => 5,
                'pricePerPiece' => 5.99,
            ],
        ];
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($value, $this->constraint);
    }

    #[Test]
    public function it_should_reject_non_array_value(): void
    {
        $value = 'not-an-array';
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($this->violationBuilder);

        $this->validator->validate($value, $this->constraint);
    }

    #[Test]
    public function it_should_reject_array_with_non_array_product(): void
    {
        $value = [
            'not-a-product-array',
        ];
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($this->violationBuilder);

        $this->validator->validate($value, $this->constraint);
    }

    #[Test]
    #[DataProvider('invalidProductDataProvider')]
    public function it_should_reject_product_with_missing_keys(array $invalidProduct): void
    {
        $value = [$invalidProduct];
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($this->violationBuilder);

        $this->validator->validate($value, $this->constraint);
    }

    public static function invalidProductDataProvider(): array
    {
        return [
            'missing id' => [
                [
                    'quantity' => 2,
                    'pricePerPiece' => 10.50,
                ],
            ],
            'missing quantity' => [
                [
                    'id' => 'product-1',
                    'pricePerPiece' => 10.50,
                ],
            ],
            'missing pricePerPiece' => [
                [
                    'id' => 'product-1',
                    'quantity' => 2,
                ],
            ],
            'missing id and quantity' => [
                [
                    'pricePerPiece' => 10.50,
                ],
            ],
            'missing id and pricePerPiece' => [
                [
                    'quantity' => 2,
                ],
            ],
            'missing quantity and pricePerPiece' => [
                [
                    'id' => 'product-1',
                ],
            ],
            'empty array' => [
                [],
            ],
            'extra keys only' => [
                [
                    'name' => 'Product Name',
                    'description' => 'Product Description',
                ],
            ],
        ];
    }

    #[Test]
    public function it_should_reject_mixed_valid_and_invalid_products(): void
    {
        $value = [
            [
                'id' => 'product-1',
                'quantity' => 2,
                'pricePerPiece' => 10.50,
            ],
            [
                'id' => 'product-2',
                'quantity' => 1,
            ],
            [
                'id' => 'product-3',
                'quantity' => 5,
                'pricePerPiece' => 5.99,
            ],
        ];
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($this->violationBuilder);

        $this->validator->validate($value, $this->constraint);
    }

    #[Test]
    public function it_should_validate_product_with_extra_keys(): void
    {
        $value = [
            [
                'id' => 'product-1',
                'quantity' => 2,
                'pricePerPiece' => 10.50,
                'name' => 'Extra Product Name',
                'description' => 'Extra description',
            ],
        ];
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($value, $this->constraint);
    }

    #[Test]
    public function it_should_validate_product_with_different_data_types(): void
    {
        $value = [
            [
                'id' => 123,
                'quantity' => '2',
                'pricePerPiece' => '10.50',
            ],
        ];
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($value, $this->constraint);
    }

    #[Test]
    public function it_should_validate_product_with_null_values(): void
    {
        $value = [
            [
                'id' => null,
                'quantity' => null,
                'pricePerPiece' => null,
            ],
        ];
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($value, $this->constraint);
    }
}
