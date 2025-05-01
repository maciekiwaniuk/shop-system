<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\DTO\Validation;

use App\Common\Application\DTO\AbstractValidationDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Sequentially;

class SearchProductsDTO extends AbstractValidationDTO
{
    #[Sequentially([
        new NotBlank(['message' => 'Search phrase cannot be blank.']),
        new Length([
            'min' => 2,
            'minMessage' => 'Search phrase should be at least 2 characters long.',
            'max' => 100,
            'maxMessage' => 'Search phrase can be up to 100 characters long.',
        ]),
    ])]
    #[Groups(['default'])]
    public readonly ?string $phrase;

    public function __construct(
        ?string $phrase,
    ) {
        $this->phrase = $phrase;
    }
}
