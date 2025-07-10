<?php

declare(strict_types=1);

namespace App\Common\Application\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

class PaginationUuidDTO extends AbstractValidationDTO
{
    #[Sequentially([
        new Type(type: 'string', message: 'Cursor must be a string.'),
        new Uuid(message: 'Cursor UUID must be a valid UUID.'),
    ])]
    public readonly ?string $cursor;

    #[Sequentially([
        new NotBlank(message: 'Limit cannot be blank.'),
        new Positive(message: 'Limit must be valid number.'),
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
