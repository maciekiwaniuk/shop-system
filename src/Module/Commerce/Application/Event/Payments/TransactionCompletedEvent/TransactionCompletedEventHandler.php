<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Event\Payments\TransactionCompletedEvent;

use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Module\Commerce\Application\Command\ChangeOrderStatus\ChangeOrderStatusCommand;
use App\Module\Commerce\Application\DTO\Validation\ChangeOrderStatusDTO;
use App\Module\Commerce\Domain\Enum\OrderStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class TransactionCompletedEventHandler
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(TransactionCompletedEvent $event): void
    {
        $result = $this->syncCommandBus->handle(new ChangeOrderStatusCommand(
            dto: new ChangeOrderStatusDTO(
                status: OrderStatus::COMPLETED,
            ),
            uuid: $event->transactionId,
        ));
        if (!$result->success) {
            $this->logger->error('Failed to handle transaction completed event', [
                'transaction_id' => $event->transactionId,
                'status_code' => $result->statusCode,
            ]);
        }
    }
}
