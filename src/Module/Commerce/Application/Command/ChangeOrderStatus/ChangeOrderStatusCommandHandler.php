<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\ChangeOrderStatus;

use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use App\Module\Commerce\Domain\Entity\OrderStatusUpdate;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Module\Commerce\Domain\Repository\OrderStatusUpdateRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class ChangeOrderStatusCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private OrderStatusUpdateRepositoryInterface $orderStatusUpdateRepository,
        private OrderRepositoryInterface $orderRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ChangeOrderStatusCommand $command): CommandResult
    {
        try {
            $changeOrderStatus = new OrderStatusUpdate(
                order: $this->orderRepository->getReference($command->uuid),
                status: $command->dto->status,
            );
            $this->orderStatusUpdateRepository->save($changeOrderStatus, true);
            return new CommandResult(success: true, statusCode: Response::HTTP_OK);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to change order status', [
                'new_status' => $command->dto->status->value,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
