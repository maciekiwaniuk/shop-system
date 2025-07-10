<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\DTO\Validation;

use App\Common\Application\DTO\AbstractValidationDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Sequentially;

class UpdateProductDTO extends AbstractValidationDTO
{
    #[Sequentially([
        new NotBlank(message: 'Name cannot be blank.'),
        new Length(
            min: 2,
            max: 100,
            minMessage: 'Name must be at least {{ limit }} characters long.',
            maxMessage: 'Name cannot be longer than {{ limit }} characters.',
        ),
    ])]
    #[Groups(['default'])]
    public readonly ?string $name;

    #[Sequentially([
        new NotBlank(message: 'Price cannot be blank.'),
        new Positive(message: 'Price must be a positive number.'),
    ])]
    #[Groups(['default'])]
    public readonly ?float $price;

    public function __construct(
        ?string $name,
        ?float $price,
    ) {
        $this->name = $name;
        $this->price = $price;
    }
}
