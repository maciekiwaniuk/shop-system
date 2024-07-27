<?php

declare(strict_types=1);

namespace App\Module\Product\Application\ValueResolver\ProductsController;

use App\Shared\Application\DTO\PaginationIdDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('get_paginated_products')]
class GetPaginatedProductsValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<PaginationIdDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dto = new PaginationIdDTO(
            offset: $request->query->get('offset') ?? null,
            limit: $request->query->get('limit') ?? null,
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
