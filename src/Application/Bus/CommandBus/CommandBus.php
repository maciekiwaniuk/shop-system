<?php

declare(strict_types=1);

namespace App\Application\Bus\CommandBus;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
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
        $handledStamps = ($this->bus->dispatch($command))
            ->all(HandledStamp::class);

        $handledStamp = $handledStamps[0];
        if (method_exists($handledStamp, 'getResult')) {
            $commandResult = $handledStamp->getResult();
        }

        if (
            !$handledStamps
            || count($handledStamps) > 1
            || !isset($commandResult)
            || !$commandResult instanceof CommandResult
        ) {
            $this->logger->error('Something went wrong while handling action in command bus');
            return new CommandResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $commandResult;
    }
}
