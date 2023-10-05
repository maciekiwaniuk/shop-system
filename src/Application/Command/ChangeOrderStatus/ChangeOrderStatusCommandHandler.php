<?php

declare(strict_types=1);

namespace App\Application\Command\ChangeOrderStatus;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandHandlerInterface;
use App\Domain\Entity\OrderStatusUpdate;
use App\Domain\Entity\Product;
use App\Domain\Repository\OrderStatusUpdateRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
                order: $this->entityManager->getReference(Product::class, $command->uuid),
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
