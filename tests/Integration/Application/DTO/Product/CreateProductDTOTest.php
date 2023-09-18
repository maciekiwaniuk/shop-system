<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\DTO\Product;

use App\Application\DTO\Product\CreateProductDTO;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateProductDTOTest extends AbstractIntegrationTestCase
{
    protected ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValid(): void
    {
        $dto = new CreateProductDTO(
            name: 'Valid name',
            price: 34.23
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(0, $errors);
    }

    public function invalidNameProvider(): iterable
    {
        yield [new CreateProductDTO(
            name: '',
            price: 32.33
        ), 2];
        yield [new CreateProductDTO(
            name: 'a',
            price: 2.33
        ), 1];
        yield [new CreateProductDTO(
            // 101 chars
            name: 'lllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll',
            price: 1.34
        ), 1];
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testInvalidName(CreateProductDTO $dto, int $expectedCount): void
    {
        $errors = $this->validator->validate($dto);

        $this->assertCount($expectedCount, $errors);
    }

    public function invalidPriceProvider(): iterable
    {
        yield [new CreateProductDTO(
            name: 'abc',
            price: 0
        ), 1];
        yield [new CreateProductDTO(
            name: 'dsadsadsa',
            price: -2
        ), 1];
    }

    /**
     * @dataProvider invalidPriceProvider
     */
    public function testInvalidPrice(CreateProductDTO $dto, int $expectedCount): void
    {
        $errors = $this->validator->validate($dto);

        $this->assertCount($expectedCount, $errors);
    }
}
