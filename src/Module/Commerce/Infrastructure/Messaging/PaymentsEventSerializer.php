<?php

declare(strict_types=1);

namespace App\Module\Commerce\Infrastructure\Messaging;

use App\Module\Commerce\Application\Event\Payments\TransactionCompletedEvent\TransactionCompletedEvent;
use App\Module\Commerce\Application\Event\Payments\TransactionCanceledEvent\TransactionCanceledEvent;
use InvalidArgumentException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

readonly class PaymentsEventSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = json_decode(json: $encodedEnvelope['body'], associative: true, flags: JSON_THROW_ON_ERROR);
        $routingKey = $body['routing_key'] ?? '';

        $message = match ($routingKey) {
            'transaction_completed' => new TransactionCompletedEvent(
                transactionId: $body['transaction_id'],
                completedAt: $body['completed_at'],
            ),
            'transaction_canceled' => new TransactionCanceledEvent(
                transactionId: $body['transaction_id'],
                canceledAt: $body['canceled_at'],
            ),
            default => throw new InvalidArgumentException("Unknown routing key: $routingKey"),
        };

        return new Envelope($message);
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        return [
            'body' => json_encode($message, JSON_THROW_ON_ERROR),
            'headers' => [],
        ];
    }
}
