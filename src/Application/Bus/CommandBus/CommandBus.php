<?php

namespace App\Application\Bus\CommandBus;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus extends MessageBus implements CommandBusInterface
{
    public function __construct(
        protected readonly LoggerInterface $logger,
        iterable $middlewareHandlers = []
    ) {
        parent::__construct($middlewareHandlers);
    }

    public function handle(CommandInterface $command): CommandResult
    {
        $envelope = $this->dispatch($command);
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