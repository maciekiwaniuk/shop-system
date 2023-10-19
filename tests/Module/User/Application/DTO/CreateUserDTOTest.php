<?php

declare(strict_types=1);

namespace App\Tests\Module\User\Application\DTO;

use App\Module\User\Application\DTO\CreateUserDTO;
use App\Tests\AbstractUnitTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserDTOTest extends AbstractUnitTestCase
{
    protected object $validator;
    protected string $exampleValidEmail = 'example@email.com';
    protected string $exampleValidPassword = 'example123';
    protected string $exampleValidName = 'John';
    protected string $exampleValidSurname = 'Muller';

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidData(): void
    {
        $dto = new CreateUserDTO(
            email: $this->exampleValidEmail,
            password: $this->exampleValidPassword,
            name: $this->exampleValidName,
            surname: $this->exampleValidSurname
        );

        $errors = $this->validator->validate($dto);

        $this->assertEmpty($errors);
    }

    public function invalidEmailProvider(): iterable
    {
        yield [''];
        yield ['mailWithoutAt'];
        yield [str_repeat('o', 101) . '@example.com'];
    }

    public function testInvalidEmail(string $email): void
    {
        $dto = new CreateUserDTO(
            email: $email,
            password: $this->exampleValidPassword,
            name: $this->exampleValidName,
            surname: $this->exampleValidSurname
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public function invalidPasswordProvider(): iterable
    {
        yield [''];
        yield ['1'];
        yield [str_repeat('o', 101)];
    }

    public function testInvalidPassword(string $password): void
    {
        $dto = new CreateUserDTO(
            email: $this->exampleValidEmail,
            password: $password,
            name: $this->exampleValidName,
            surname: $this->exampleValidSurname
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public function invalidNameProvider(): iterable
    {
        yield [''];
        yield ['1'];
        yield [str_repeat('o', 101)];
    }

    public function testInvalidName(string $name): void
    {
        $dto = new CreateUserDTO(
            email: $this->exampleValidEmail,
            password: $this->exampleValidPassword,
            name: $name,
            surname: $this->exampleValidSurname
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public function invalidSurnameProvider(): iterable
    {
        yield [''];
        yield ['1'];
        yield [str_repeat('o', 101)];
    }

    public function testInvalidSurname(string $surname): void
    {
        $dto = new CreateUserDTO(
            email: $this->exampleValidEmail,
            password: $this->exampleValidPassword,
            name: $this->exampleValidName,
            surname: $surname
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }
}
