<?php

declare(strict_types=1);

namespace App\Tests\Module\Auth\Application\DTO;

use App\Module\Auth\Application\DTO\Validation\CreateUserDTO;
use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserDTOTest extends AbstractIntegrationTestCase
{
    private ValidatorInterface $validator;
    private string $exampleValidEmail = 'example@email.com';
    private string $exampleValidPassword = 'example123';
    private string $exampleValidName = 'John';
    private string $exampleValidSurname = 'Muller';

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /** @test */
    public function it_should_pass_validation_when_data_is_correct(): void
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
     * @test
     */
    public function it_should_not_pass_validation_when_email_is_invalid(string $email): void
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
     * @test
     */
    public function it_should_not_pass_validation_when_password_is_invalid(string $password): void
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
     * @test
     */
    public function it_should_not_pass_validation_when_name_is_invalid(string $name): void
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
     * @test
     */
    public function it_should_not_pass_validation_when_surname_is_invalid(string $surname): void
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

    /** @test */
    public function it_should_not_pass_validation_when_email_is_duplicated(): void
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
