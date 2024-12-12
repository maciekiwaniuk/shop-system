<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\CommandBus;

use App\Common\Application\SyncCommand\AsyncCommandInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

readonly class AsyncCommandBus implements AsyncCommandBusInterface
{
    public function __construct(
        protected MessageBusInterface $bus,
    ) {
    }

    public function handle(AsyncCommandInterface $command): void
    {
        $this->bus->dispatch($command);
    }
}
