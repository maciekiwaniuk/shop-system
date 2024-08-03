<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\ValueResolver\OrdersController;

use App\Common\Application\DTO\PaginationUuidDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('get_paginated_orders')]
readonly class GetPaginatedOrdersValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<PaginationUuidDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dto = new PaginationUuidDTO(
            cursor: $request->query->get('cursor') ?? null,
            limit: ($limit = $request->query->get('limit')) ? (int) $limit : null,
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
