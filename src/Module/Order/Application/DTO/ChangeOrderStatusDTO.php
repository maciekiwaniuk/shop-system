<?php

declare(strict_types=1);

namespace App\Module\Order\Application\DTO;

use App\Module\Order\Domain\Enum\OrderStatus;
use App\Shared\Application\DTO\BaseDTO;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeOrderStatusDTO extends BaseDTO
{
    #[NotBlank]
    public readonly OrderStatus $status;

    public function __construct(
        OrderStatus $status
    ) {
        $this->status = $status;
    }
}
