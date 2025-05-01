<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\ValueResolver\ProductsController;

use App\Common\Application\DTO\PaginationIdDTO;
use App\Module\Commerce\Application\DTO\Validation\SearchProductsDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('search_products_dto')]
readonly class SearchProductsValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<SearchProductsDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dto = new SearchProductsDTO($request->query->get('phrase'));

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
