<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\ValueResolver\OrdersController;

use App\Module\Commerce\Application\DTO\Validation\CreateOrderDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('create_order_dto')]
readonly class CreateOrderValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<CreateOrderDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->toArray();

        $dto = new CreateOrderDTO(
            products: $data['products'] ?? null,
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
