<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO;

use App\Module\Auth\Domain\Entity\User;
use App\Common\Application\Constraint\UniqueFieldInEntity;
use App\Common\Application\DTO\AbstractDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateUserDTO extends AbstractDTO
{
    #[Sequentially([
        new NotBlank(['message' => 'Email cannot be blank.']),
        new Email(['message' => 'Email must be valid.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Email should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Email can be up to 100 characters long.',
        ]),
        new UniqueFieldInEntity(field: 'email', entityClassName: User::class),
    ])]
    #[Groups(['default'])]
    public readonly ?string $email;

    #[Sequentially([
        new NotBlank(['message' => 'Name cannot be blank.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Name should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Name can be up to 100 characters long.',
        ]),
    ])]
    #[Groups(['default'])]
    public readonly ?string $name;

    #[Sequentially([
        new NotBlank(['message' => 'Password cannot be blank.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Password should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Password can be up to 100 characters long.',
        ]),
    ])]
    #[Groups(['default'])]
    public readonly ?string $password;

    #[Sequentially([
        new NotBlank(['message' => 'Surname cannot be blank.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Surname should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Surname can be up to 100 characters long.',
        ]),
    ])]
    #[Groups(['default'])]
    public readonly ?string $surname;

    public function __construct(
        ?string $email,
        ?string $password,
        ?string $name,
        ?string $surname,
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
    }
}
