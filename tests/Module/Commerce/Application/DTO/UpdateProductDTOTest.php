<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\DTO;

use App\Module\Commerce\Application\DTO\Validation\UpdateProductDTO;
use App\Tests\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Group('integration')]
class UpdateProductDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;
    private string $exampleValidName = 'Example name';
    private float $exampleValidPrice = 45.33;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    #[Test]
    public function it_should_pass_validation_when_data_is_valid(): void
    {
        $dto = new UpdateProductDTO(
            name: $this->exampleValidName,
            price: $this->exampleValidPrice,
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    #[Test]
    #[DataProvider('invalidNameProvider')]
    public function it_should_not_pass_validation_when_name_is_invalid(string $name): void
    {
        $dto = new UpdateProductDTO(
            name: $name,
            price: 45.33,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public static function invalidNameProvider(): iterable
    {
        yield [''];
        yield ['1'];
        yield [str_repeat('d', 101)];
    }

    #[Test]
    #[DataProvider('invalidPriceProvider')]
    public function it_should_not_pass_validation_when_price_is_invalid(float $price): void
    {
        $dto = new UpdateProductDTO(
            name: 'Example name',
            price: $price,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public static function invalidPriceProvider(): iterable
    {
        yield [0.0];
        yield [-1.1];
    }
}
