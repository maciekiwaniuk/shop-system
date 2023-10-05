<?php

declare(strict_types=1);

namespace App\Application\DTO\Order;

use App\Application\DTO\BaseDTO;
use App\Domain\Enum\OrderStatus;
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
