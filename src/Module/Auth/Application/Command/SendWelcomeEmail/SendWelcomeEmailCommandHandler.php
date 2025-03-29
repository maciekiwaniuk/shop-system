<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\SendWelcomeEmail;

use App\Common\Application\AsyncCommand\AsyncCommandHandlerInterface;

class SendWelcomeEmailCommandHandler implements AsyncCommandHandlerInterface
{
    public function __construct(

    ) {
    }

    public function __invoke(SendWelcomeEmailCommand $command)
    {
        // sending welcome email
    }
}