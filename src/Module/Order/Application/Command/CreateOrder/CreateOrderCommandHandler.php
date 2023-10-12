<?php

declare(strict_types=1);

namespace App\Module\Order\Application\Command\CreateOrder;

use App\Module\Order\Domain\Entity\Order;
use App\Module\Order\Infrastructure\Doctrine\Repository\OrderRepository;
use App\Module\Product\Domain\Entity\Product;
use App\Module\User\Domain\Entity\User;
use App\Shared\Application\BusResult\CommandResult;
use App\Shared\Application\Command\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
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
        protected readonly EntityManagerInterface $entityManager,
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
            foreach ($command->dto->products as $product) {
                $order->createAndAddOrderProduct(
                    $this->entityManager->getReference(Product::class, $product['id']),
                    $product['quantity'],
                    $product['pricePerPiece']
                );
            }
            $this->orderRepository->save($order, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
