<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\ValueResolver\ProductsController;

use App\Module\Commerce\Application\DTO\UpdateProductDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('update_product_dto')]
class UpdateProductValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @return iterable<UpdateProductDTO>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->toArray();

        $dto = new UpdateProductDTO(
            name: $data['name'] ?? null,
            price: $data['price'] ? (float) $data['price'] : null,
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $dto->setErrors($errors);
        }

        yield $dto;
    }
}
