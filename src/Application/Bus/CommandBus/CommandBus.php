<?php

declare(strict_types=1);

namespace App\Application\Bus\CommandBus;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus implements CommandBusInterface
{
    public function __construct(
        protected readonly MessageBusInterface $bus,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function handle(CommandInterface $command): CommandResult
    {
        $envelope = $this->bus->dispatch($command);
        $handledStamps = $envelope->all(HandledStamp::class);

        $result = $handledStamps[0]->getResult();
        if (!$handledStamps || count($handledStamps) > 1 || !$result instanceof CommandResult) {
            return new CommandResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $result;
    }
}