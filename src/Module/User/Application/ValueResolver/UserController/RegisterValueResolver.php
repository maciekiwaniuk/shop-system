<?php

declare(strict_types=1);

namespace App\Module\User\Application\ValueResolver\UserController;

use App\Module\User\Application\DTO\CreateUserDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('create_user_dto')]
class RegisterValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<CreateUserDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->toArray();

        $dto = new CreateUserDTO(
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            name: $data['name'] ?? null,
            surname: $data['surname'] ?? null,
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
