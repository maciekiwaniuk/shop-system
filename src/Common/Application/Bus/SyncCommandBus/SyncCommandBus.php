<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\SyncCommandBus;

use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

readonly class SyncCommandBus implements SyncCommandBusInterface
{
    public function __construct(
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(SyncCommandInterface $command): CommandResult
    {
        $handledStamps = ($this->bus->dispatch($command))
            ->all(HandledStamp::class);

        [$handledStamp] = $handledStamps;
        if (method_exists($handledStamp, 'getResult')) {
            $commandResult = $handledStamp->getResult();
        }

        if (
            !$handledStamps
            || count($handledStamps) > 1
            || !isset($commandResult)
            || !$commandResult instanceof CommandResult
        ) {
            $this->logger->error('Sync command bus handler validation failed', [
                'command_class' => get_class($command),
                'handled_stamps_count' => count($handledStamps),
                'has_command_result' => isset($commandResult),
                'command_result_class' => isset($commandResult) ? get_class($commandResult) : null,
                'expected_result_class' => CommandResult::class,
            ]);
            return new CommandResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return $commandResult;
    }
}
