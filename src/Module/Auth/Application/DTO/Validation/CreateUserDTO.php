<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO\Validation;

use App\Common\Application\Constraint\UniqueFieldInEntity;
use App\Common\Application\DTO\AbstractValidationDTO;
use App\Module\Auth\Domain\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateUserDTO extends AbstractValidationDTO
{
    #[Sequentially([
        new NotBlank(message: 'Email cannot be blank.'),
        new Email(message: 'Email must be valid.'),
        new Length(
            min: 3,
            max: 100,
            minMessage: 'Email must be at least {{ limit }} characters long.',
            maxMessage: 'Email cannot be longer than {{ limit }} characters.',
        ),
        new UniqueFieldInEntity(field: 'email', entityClassName: User::class),
    ])]
    #[Groups(['default'])]
    public readonly ?string $email;

    #[Sequentially([
        new NotBlank(message: 'Name cannot be blank.'),
        new Length(
            min: 2,
            max: 100,
            minMessage: 'Name must be at least {{ limit }} characters long.',
            maxMessage: 'Name cannot be longer than {{ limit }} characters.',
        ),
    ])]
    #[Groups(['default'])]
    public readonly ?string $name;

    #[Sequentially([
        new NotBlank(message: 'Password cannot be blank.'),
        new Length(
            min: 8,
            max: 100,
            minMessage: 'Password must be at least {{ limit }} characters long.',
            maxMessage: 'Password cannot be longer than {{ limit }} characters.',
        ),
    ])]
    #[Groups(['default'])]
    public readonly ?string $password;

    #[Sequentially([
        new NotBlank(message: 'Surname cannot be blank.'),
        new Length(
            min: 2,
            max: 100,
            minMessage: 'Surname must be at least {{ limit }} characters long.',
            maxMessage: 'Surname cannot be longer than {{ limit }} characters.',
        ),
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
