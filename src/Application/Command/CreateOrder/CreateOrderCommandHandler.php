<?php

declare(strict_types=1);

namespace App\Application\Command\CreateOrder;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandHandlerInterface;
use App\Domain\Entity\Order;
use App\Infrastructure\Doctrine\Repository\OrderRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class CreateOrderCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        protected readonly OrderRepository $orderRepository,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(CreateOrderCommand $command): CommandResult
    {
        try {
            $order = new Order(
                name: $command->dto->name
            );

            $this->orderRepository->save($order);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}