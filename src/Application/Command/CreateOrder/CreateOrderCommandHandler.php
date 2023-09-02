<?php

declare(strict_types=1);

namespace App\Application\Command\CreateOrder;

use App\Application\Command\CommandResult;
use App\Domain\Entity\Order;
use App\Infrastructure\Doctrine\Repository\OrderRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateOrderCommandHandler
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
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return new CommandResult(failure: true);
        }

        return new CommandResult(
            success: true,
            data: $order
        );
    }
}