<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateClient;

use App\Common\Application\SyncCommand\SyncCommandInterface;
use App\Module\Commerce\Application\DTO\Communication\CreateClientDTO;

readonly class CreateClientCommand implements SyncCommandInterface
{
    public function __construct(
        public CreateClientDTO $dto,
    ) {
    }
}
