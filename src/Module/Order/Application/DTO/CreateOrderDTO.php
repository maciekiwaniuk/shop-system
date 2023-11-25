<?php

declare(strict_types=1);

namespace App\Module\Order\Application\DTO;

use App\Module\Order\Application\Constraint\ProductsArray;
use App\Shared\Application\DTO\AbstractDTO;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateOrderDTO extends AbstractDTO
{
    /**
     * @var array<array{id: int, quantity: int, pricePerPiece: float}>|null $products
     */
    #[Sequentially([
        new NotBlank(['message' => 'Order must have products.']),
        new ProductsArray()
    ])]
    public readonly ?array $products;

    /**
     * @param array<array{id: int, quantity: int, pricePerPiece: float}>|null $products
     */
    public function __construct(
        ?array $products
    ) {
        $this->products = $products;
    }
}
