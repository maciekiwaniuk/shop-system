<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Messenger;

use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Throwable;

final readonly class ErrorLoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $dispatchedLocally = $envelope->last(ReceivedStamp::class) === null;
        if ($dispatchedLocally) {
            return $stack->next()->handle($envelope, $stack);
        }

        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (Throwable $exception) {
            $message = $envelope->getMessage();
            $messageClass = $message::class;

            $context = [
                'exception_class' => $exception::class,
                'exception_message' => $exception->getMessage(),
                'message_class' => $messageClass,
                'message_data' => $this->extractMessageData($message),
                'trace' => $exception->getTraceAsString(),
            ];

            $this->logger->error(
                sprintf(
                    'Error handling message %s: %s',
                    $messageClass,
                    $exception->getMessage(),
                ),
                $context,
            );

            throw $exception;
        }
    }

    private function extractMessageData(object $message): array
    {
        $data = [];

        try {
            $reflection = new ReflectionClass($message);
            foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyName = $property->getName();
                $data[$propertyName] = $property->getValue($message);
            }
        } catch (Throwable) {
            $data['_extraction_failed'] = true;
        }

        return $data;
    }
}

