<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\DTO;

use App\Module\Commerce\Domain\Enum\OrderStatus;
use App\Shared\Application\DTO\AbstractDTO;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class ChangeOrderStatusDTO extends AbstractDTO
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
