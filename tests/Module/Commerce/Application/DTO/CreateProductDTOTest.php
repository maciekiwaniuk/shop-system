<?php

declare(strict_types=1);

namespace App\Tests\Module\Commerce\Application\DTO;

use App\Module\Commerce\Application\DTO\Validation\CreateProductDTO;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateProductDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;
    private string $exampleValidName = 'Example name';
    private float $exampleValidPrice = 45.33;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /** @test */
    public function it_should_pass_validation_when_data_is_valid(): void
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
     * @test
     */
    public function it_should_not_pass_validation_when_name_is_invalid(string $name): void
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
     * @test
     */
    public function it_should_not_pass_validation_when_price_is_invalid(float $price): void
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
