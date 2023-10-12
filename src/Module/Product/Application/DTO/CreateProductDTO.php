<?php

declare(strict_types=1);

namespace App\Module\Product\Application\DTO;

use App\Shared\Application\DTO\BaseDTO;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class CreateProductDTO extends BaseDTO
{
    #[NotBlank]
    #[Length([
        'min' => 2,
        'minMessage' => 'Name should be at least 2 characters long.',
        'max' => 100,
        'maxMessage' => 'Name can be up to 100 characters long.'
    ])]
    public readonly string $name;

    #[NotBlank]
    #[Positive([
        'message' => 'Price must be valid number.'
    ])]
    public readonly float $price;

    public function __construct(
        string $name,
        float $price
    ) {
        $this->name = $name;
        $this->price = $price;
    }
}
