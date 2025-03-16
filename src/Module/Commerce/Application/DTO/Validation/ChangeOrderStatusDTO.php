<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\DTO\Validation;

use App\Common\Application\DTO\AbstractValidationDTO;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class ChangeOrderStatusDTO extends AbstractValidationDTO
{
    #[Sequentially([
        new NotBlank(['message' => 'Order status cannot be blank.']),
    ])]
    #[Groups(['default'])]
    public readonly ?OrderStatus $status;

    public function __construct(
        ?OrderStatus $status,
    ) {
        $this->status = $status;
    }
}
