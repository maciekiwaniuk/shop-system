<?php

declare(strict_types=1);

namespace Module\Product\Application\DTO;

use App\Module\Product\Application\DTO\CreateProductDTO;
use App\Tests\AbstractApplicationTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateProductDTOTest extends AbstractApplicationTestCase
{
    protected object $validator;
    protected string $exampleValidName = 'Example name';
    protected float $exampleValidPrice = 45.33;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidData(): void
    {
        $dto = new CreateProductDTO(
            name: $this->exampleValidName,
            price: $this->exampleValidPrice
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }
}
