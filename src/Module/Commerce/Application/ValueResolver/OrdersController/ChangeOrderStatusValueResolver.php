<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\ValueResolver\OrdersController;

use App\Module\Commerce\Application\DTO\ChangeOrderStatusDTO;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('change_order_status_dto')]
readonly class ChangeOrderStatusValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<ChangeOrderStatusDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->toArray();

        $dto = new ChangeOrderStatusDTO(
            status: isset($data['status']) ? OrderStatus::from($data['status']) : null,
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
