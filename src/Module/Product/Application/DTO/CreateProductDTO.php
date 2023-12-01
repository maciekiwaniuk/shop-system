<?php

declare(strict_types=1);

namespace App\Module\Product\Application\DTO;

use App\Shared\Application\DTO\AbstractDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateProductDTO extends AbstractDTO
{
    #[Sequentially([
        new NotBlank(['message' => 'Name cannot be blank.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Name should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Name can be up to 100 characters long.'
        ])
    ])]
    #[Groups(['default'])]
    public readonly ?string $name;

    #[Sequentially([
        new NotBlank(['message' => 'Price cannot be blank.']),
        new Positive([
            'message' => 'Price must be valid number.'
        ])
    ])]
    #[Groups(['default'])]
    public readonly ?float $price;

    public function __construct(
        ?string $name,
        ?float $price
    ) {
        $this->name = $name;
        $this->price = $price;
    }
}
