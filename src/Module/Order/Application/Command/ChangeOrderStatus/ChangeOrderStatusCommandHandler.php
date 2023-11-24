<?php

declare(strict_types=1);

namespace App\Module\Order\Application\Command\ChangeOrderStatus;

use App\Module\Order\Domain\Entity\Order;
use App\Module\Order\Domain\Entity\OrderStatusUpdate;
use App\Module\Order\Domain\Repository\OrderStatusUpdateRepositoryInterface;
use App\Shared\Application\BusResult\CommandResult;
use App\Shared\Application\Command\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class ChangeOrderStatusCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        protected readonly OrderStatusUpdateRepositoryInterface $orderStatusUpdateRepository,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(ChangeOrderStatusCommand $command): CommandResult
    {
        try {
            $changeOrderStatus = new OrderStatusUpdate(
                order: $this->entityManager->getReference(Order::class, $command->uuid),
                status: $command->dto->status
            );
            $this->orderStatusUpdateRepository->save($changeOrderStatus, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
