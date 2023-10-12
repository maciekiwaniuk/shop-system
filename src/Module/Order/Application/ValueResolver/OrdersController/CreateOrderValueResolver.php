<?php

declare(strict_types=1);

namespace App\Module\Order\Application\ValueResolver\OrdersController;

use App\Module\Order\Application\DTO\CreateOrderDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('create_order_dto')]
class CreateOrderValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @return iterable<CreateOrderDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->toArray();

        $dto = new CreateOrderDTO(
            products: $data['products']
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
