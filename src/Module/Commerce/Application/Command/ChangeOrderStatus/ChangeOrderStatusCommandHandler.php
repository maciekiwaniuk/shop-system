<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\ChangeOrderStatus;

use App\Common\Application\AsyncCommand\AsyncCommandHandlerInterface;
use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Commerce\Domain\Entity\OrderStatusUpdate;
use App\Module\Commerce\Domain\Repository\OrderStatusUpdateRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class ChangeOrderStatusCommandHandler implements AsyncCommandHandlerInterface
{
    public function __construct(
        private OrderStatusUpdateRepositoryInterface $orderStatusUpdateRepository,
        private EntityManagerInterface $commerceEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ChangeOrderStatusCommand $command): void
    {
        try {
            $changeOrderStatus = new OrderStatusUpdate(
                order: $this->commerceEntityManager->getReference(Order::class, $command->uuid),
                status: $command->dto->status,
            );
            $this->orderStatusUpdateRepository->save($changeOrderStatus, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
        }
    }
}
