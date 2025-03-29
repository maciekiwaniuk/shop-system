<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\SendWelcomeEmail;

use App\Common\Application\AsyncCommand\AsyncCommandInterface;
use App\Module\Auth\Application\DTO\Communication\SendWelcomeEmailDTO;

readonly class SendWelcomeEmailCommand implements AsyncCommandInterface
{
    public function __construct(
        public SendWelcomeEmailDTO $dto,
    ) {
    }
}