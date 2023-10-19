<?php

declare(strict_types=1);

namespace App\Tests\Module\User\Application\DTO;

use App\Module\User\Application\DTO\CreateUserDTO;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserDTOTest extends AbstractUnitTestCase
{
    protected object $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidData(): void
    {
        $dto = new CreateUserDTO(
            email: 'example@email.com',
            password: 'example123',
            name: 'John',
            surname: 'Muller'
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }
}
