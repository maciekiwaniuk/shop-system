<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\ChangeOrderStatus;

use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Commerce\Domain\Entity\OrderStatusUpdate;
use App\Module\Commerce\Domain\Repository\OrderStatusUpdateRepositoryInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

readonly class ChangeOrderStatusCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        protected OrderStatusUpdateRepositoryInterface $orderStatusUpdateRepository,
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger,
    ) {
    }

    public function __invoke(ChangeOrderStatusCommand $command): CommandResult
    {
        try {
            $changeOrderStatus = new OrderStatusUpdate(
                order: $this->entityManager->getReference(Order::class, $command->uuid),
                status: $command->dto->status,
            );
            $this->orderStatusUpdateRepository->save($changeOrderStatus, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
