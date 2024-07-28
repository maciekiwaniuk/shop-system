<?php

declare(strict_types=1);

namespace App\Tests\Module\Auth\Application\DTO;

use App\Module\Auth\Application\DTO\CreateUserDTO;
use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserDTOTest extends AbstractIntegrationTestCase
{
    protected readonly ValidatorInterface $validator;
    protected string $exampleValidEmail = 'example@email.com';
    protected string $exampleValidPassword = 'example123';
    protected string $exampleValidName = 'John';
    protected string $exampleValidSurname = 'Muller';

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidData(): void
    {
        $dto = new CreateUserDTO(
            email: $this->exampleValidEmail,
            password: $this->exampleValidPassword,
            name: $this->exampleValidName,
            surname: $this->exampleValidSurname,
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

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testInvalidEmail(string $email): void
    {
        $dto = new CreateUserDTO(
            email: $email,
            password: $this->exampleValidPassword,
            name: $this->exampleValidName,
            surname: $this->exampleValidSurname,
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

    /**
     * @dataProvider invalidPasswordProvider
     */
    public function testInvalidPassword(string $password): void
    {
        $dto = new CreateUserDTO(
            email: $this->exampleValidEmail,
            password: $password,
            name: $this->exampleValidName,
            surname: $this->exampleValidSurname,
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

    /**
     * @dataProvider invalidNameProvider
     */
    public function testInvalidName(string $name): void
    {
        $dto = new CreateUserDTO(
            email: $this->exampleValidEmail,
            password: $this->exampleValidPassword,
            name: $name,
            surname: $this->exampleValidSurname,
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

    /**
     * @dataProvider invalidSurnameProvider
     */
    public function testInvalidSurname(string $surname): void
    {
        $dto = new CreateUserDTO(
            email: $this->exampleValidEmail,
            password: $this->exampleValidPassword,
            name: $this->exampleValidName,
            surname: $surname,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }

    public function testDuplicateEmail(): void
    {
        $user = new User(
            email: 'duplicated@email.com',
            password: $this->exampleValidPassword,
            name: $this->exampleValidName,
            surname: $this->exampleValidSurname,
        );
        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);
        $userRepository->save($user, true);

        $dto = new CreateUserDTO(
            email: $user->getEmail(),
            password: $this->exampleValidPassword,
            name: $this->exampleValidName,
            surname: $this->exampleValidSurname,
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(1, $errors);
    }
}
