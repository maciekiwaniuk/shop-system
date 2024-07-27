<?php

declare(strict_types=1);

namespace App\Tests\Module\Product\Application\DTO;

use App\Module\Product\Application\DTO\CreateProductDTO;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateProductDTOTest extends AbstractIntegrationTestCase
{
    protected readonly ValidatorInterface $validator;
    protected string $exampleValidName = 'Example name';
    protected float $exampleValidPrice = 45.33;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidData(): void
    {
        $dto = new CreateProductDTO(
            name: $this->exampleValidName,
            price: $this->exampleValidPrice,
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testInvalidName(string $name): void
    {
        $dto = new CreateProductDTO(
            name: $name,
            price: $this->exampleValidPrice,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public function invalidNameProvider(): iterable
    {
        yield [''];
        yield ['1'];
        yield [str_repeat('d', 101)];
    }

    /**
     * @dataProvider invalidPriceProvider
     */
    public function testInvalidPrice(float $price): void
    {
        $dto = new CreateProductDTO(
            name: $this->exampleValidName,
            price: $price,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public function invalidPriceProvider(): iterable
    {
        yield [0.0];
        yield [-1.1];
    }
}
