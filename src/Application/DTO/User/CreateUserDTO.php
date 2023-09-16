<?php

declare(strict_types=1);

namespace App\Application\DTO\User;

use App\Application\Constraint\UniqueFieldInEntity;
use App\Application\DTO\BaseDTO;
use App\Domain\Entity\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateUserDTO extends BaseDTO
{
    #[NotBlank(['message' => 'Email cannot be blank.'])]
    #[Email(['message' => 'Email must be valid.'])]
    #[Length([
        'min' => 2,
        'minMessage' => 'Email should be at least 2 characters long.',
        'max' => 100,
        'maxMessage' => 'Email can be up to 100 characters long.'
    ])]
    #[UniqueFieldInEntity(field: 'email', entityClassName: User::class)]
    public readonly string $email;

    #[NotBlank(['message' => 'Password cannot be blank.'])]
    #[Length([
        'min' => 2,
        'minMessage' => 'Name should be at least 2 characters long.',
        'max' => 100,
        'maxMessage' => 'Name can be up to 100 characters long.'
    ])]
    public readonly string $name;

    #[NotBlank(['message' => 'Password cannot be blank.'])]
    #[Length([
        'min' => 2,
        'minMessage' => 'Password should be at least 2 characters long.',
        'max' => 100,
        'maxMessage' => 'Password can be up to 100 characters long.'
    ])]
    public readonly string $password;

    #[NotBlank(['message' => 'Surname cannot be blank.'])]
    #[Length([
        'min' => 2,
        'minMessage' => 'Surname should be at least 2 characters long.',
        'max' => 100,
        'maxMessage' => 'Surname can be up to 100 characters long.'
    ])]
    public readonly string $surname;

    public function __construct(
        string $email,
        string $password,
        string $name,
        string $surname
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
    }
}
