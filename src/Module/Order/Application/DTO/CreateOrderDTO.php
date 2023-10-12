<?php

declare(strict_types=1);

namespace App\Module\Order\Application\DTO;

use App\Shared\Application\DTO\BaseDTO;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateOrderDTO extends BaseDTO
{
    #[NotBlank]
    public readonly array $products;

    public function __construct(
        array $products
    ) {
        $this->products = $products;
    }
}
