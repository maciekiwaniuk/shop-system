<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateOrder;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Module\Commerce\Domain\Entity\Product;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Throwable;

#[AsMessageHandler]
readonly class CreateOrderCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(CreateOrderCommand $command): CommandResult
    {
        $user = $this->tokenStorage->getToken()->getUser();
        try {
            $order = new Order(
                $this->entityManager->getReference(Client::class, $user->getUserIdentifier())
            );
            foreach ($command->dto->products as $product) {
                $order->addProduct(
                    $this->entityManager->getReference(Product::class, $product['id']),
                    $product['quantity'],
                    $product['pricePerPiece'],
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
