<?php

declare(strict_types=1);

namespace App\Module\Order\Application\DTO;

use App\Shared\Application\DTO\BaseDTO;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateOrderDTO extends BaseDTO
{
    #[Sequentially([
        new NotBlank(['message' => 'Order must have products.'])
    ])]
    public readonly array $products;

    public function __construct(
        array $products
    ) {
        $this->products = $products;
    }
}
