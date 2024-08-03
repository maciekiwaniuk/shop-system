<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\DTO;

use App\Module\Commerce\Application\Constraint\ProductsArray;
use App\Common\Application\DTO\AbstractDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;
use OpenApi\Attributes as OA;

class CreateOrderDTO extends AbstractDTO
{
    /**
     * @var array<array{id: int, quantity: int, pricePerPiece: float}>|null $products
     */
    #[Sequentially([
        new NotBlank(['message' => 'Order must have products.']),
        new ProductsArray(),
    ])]
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            properties: [
                new OA\Property(property: 'id'),
                new OA\Property(property: 'quantity'),
                new OA\Property(property: 'pricePerPiece'),
            ],
            type: 'object',
        ),
    )]
    #[Groups(['default'])]
    public readonly ?array $products;

    /**
     * @param array<array{id: int, quantity: int, pricePerPiece: float}>|null $products
     */
    public function __construct(
        ?array $products,
    ) {
        $this->products = $products;
    }
}
