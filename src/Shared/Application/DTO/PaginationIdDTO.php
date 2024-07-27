<?php

declare(strict_types=1);

namespace App\Shared\Application\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Sequentially;

class PaginationIdDTO extends AbstractDTO
{
    #[Sequentially([
        new NotBlank(['message' => 'Offset cannot be blank.']),
        new Positive([
            'message' => 'Offset must be valid number.',
        ]),
    ])]
    public readonly ?int $offset;

    #[Sequentially([
        new NotBlank(['message' => 'Limit cannot be blank.']),
        new Positive([
            'message' => 'Limit must be valid number.',
        ]),
    ])]
    public readonly ?int $limit;

    public function __construct(
        ?int $offset,
        ?int $limit,
    ) {
        $this->offset = $offset;
        $this->limit = $limit;
    }
}
