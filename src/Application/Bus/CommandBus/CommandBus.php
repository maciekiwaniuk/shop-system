<?php

namespace App\Application\Bus\CommandBus;

use App\Application\Command\CommandResult;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus extends MessageBus implements CommandBusInterface
{
    public function handle(object $message): CommandResult
    {
        $envelope = $this->dispatch($message);
        $handledStamps = $envelope->all(HandledStamp::class);

        if (!$handledStamps) {
            throw new LogicException(sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__));
        }

        if (count($handledStamps) > 1) {
            $handlers = implode(', ', array_map(fn (HandledStamp $stamp): string => sprintf('"%s"', $stamp->getHandlerName()), $handledStamps));

            throw new LogicException(sprintf('Message of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__, count($handledStamps), $handlers));
        }

        return $handledStamps[0]->getResult();
    }
}