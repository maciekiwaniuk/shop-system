<?php

declare(strict_types=1);

namespace App\Module\Order\Application\ValueResolver\OrdersController;

use App\Module\Order\Application\DTO\ChangeOrderStatusDTO;
use App\Module\Order\Domain\Enum\OrderStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('change_order_status_dto')]
class ChangeOrderStatusValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @return iterable<ChangeOrderStatusDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->toArray();

        $dto = new ChangeOrderStatusDTO(
            status: isset($data['status']) ? OrderStatus::from($data['status']) : null
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
