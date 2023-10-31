<?php

declare(strict_types=1);

namespace App\Module\Order\Application\DTO;

use App\Module\Order\Domain\Enum\OrderStatus;
use App\Shared\Application\DTO\BaseDTO;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class ChangeOrderStatusDTO extends BaseDTO
{
    #[Sequentially([
        new NotBlank(['message' => 'Order status cannot be blank.'])
    ])]
    public readonly ?OrderStatus $status;

    public function __construct(
        ?OrderStatus $status
    ) {
        $this->status = $status;
    }
}
