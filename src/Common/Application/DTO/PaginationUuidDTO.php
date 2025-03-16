<?php

declare(strict_types=1);

namespace App\Common\Application\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Sequentially;

class PaginationUuidDTO extends AbstractValidationDTO
{
    // TODO: validation
    public readonly ?string $cursor;

    #[Sequentially([
        new NotBlank(['message' => 'Limit cannot be blank.']),
        new Positive([
            'message' => 'Limit must be valid number.',
        ]),
    ])]
    public readonly ?int $limit;

    public function __construct(
        ?string $cursor,
        ?int $limit,
    ) {
        $this->cursor = $cursor;
        $this->limit = $limit;
    }
}
