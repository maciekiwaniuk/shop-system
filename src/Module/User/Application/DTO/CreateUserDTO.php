<?php

declare(strict_types=1);

namespace App\Module\User\Application\DTO;

use App\Module\User\Domain\Entity\User;
use App\Shared\Application\Constraint\UniqueFieldInEntity;
use App\Shared\Application\DTO\BaseDTO;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateUserDTO extends BaseDTO
{
    #[Sequentially([
        new NotBlank(['message' => 'Email cannot be blank.']),
        new Email(['message' => 'Email must be valid.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Email should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Email can be up to 100 characters long.'
        ]),
        new UniqueFieldInEntity(field: 'email', entityClassName: User::class)
    ])]
    public readonly ?string $email;

    #[Sequentially([
        new NotBlank(['message' => 'Name cannot be blank.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Name should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Name can be up to 100 characters long.'
        ])
    ])]
    public readonly ?string $name;

    #[Sequentially([
        new NotBlank(['message' => 'Password cannot be blank.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Password should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Password can be up to 100 characters long.'
        ])
    ])]
    public readonly ?string $password;

    #[Sequentially([
        new NotBlank(['message' => 'Surname cannot be blank.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Surname should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Surname can be up to 100 characters long.'
        ])
    ])]
    public readonly ?string $surname;

    public function __construct(
        ?string $email,
        ?string $password,
        ?string $name,
        ?string $surname
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
    }
}
