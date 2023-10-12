<?php

declare(strict_types=1);

namespace App\Module\Product\Application\ValueResolver\ProductsController;

use App\Module\Product\Application\DTO\UpdateProductDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('create_product_dto')]
class CreateProductValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @return iterable<UpdateProductDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->toArray();

        $dto = new UpdateProductDTO(
            name: $data['name'],
            price: (float) $data['price']
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
