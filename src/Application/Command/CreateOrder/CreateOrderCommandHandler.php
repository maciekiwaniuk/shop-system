<?php

declare(strict_types=1);

namespace App\Application\Command\CreateOrder;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandHandlerInterface;
use App\Domain\Entity\Order;
use App\Domain\Entity\User;
use App\Infrastructure\Doctrine\Repository\OrderRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Throwable;

#[AsMessageHandler]
class CreateOrderCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        protected readonly OrderRepository $orderRepository,
        protected readonly LoggerInterface $logger,
        protected readonly TokenStorageInterface $tokenStorage
    ) {
    }

    public function __invoke(CreateOrderCommand $command): CommandResult
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        try {
            $order = new Order(
                user: $user
            );
            $this->orderRepository->save($order);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
