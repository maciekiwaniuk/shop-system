<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\AsyncCommandBus;

use App\Common\Application\AsyncCommand\AsyncCommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class AsyncCommandBus implements AsyncCommandBusInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function handle(AsyncCommandInterface $command): void
    {
        $this->messageBus->dispatch($command);
    }
}
