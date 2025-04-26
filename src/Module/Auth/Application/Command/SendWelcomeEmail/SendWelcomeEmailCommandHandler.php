<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\SendWelcomeEmail;

use App\Common\Application\AsyncCommand\AsyncCommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendWelcomeEmailCommandHandler implements AsyncCommandHandlerInterface
{
    public function __construct(

    ) {
    }

    public function __invoke(SendWelcomeEmailCommand $command): void
    {
        // sending welcome email
    }
}