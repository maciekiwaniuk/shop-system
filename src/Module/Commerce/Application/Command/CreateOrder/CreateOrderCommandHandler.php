<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateOrder;

use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Module\Commerce\Domain\Entity\Product;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use App\Common\Application\Security\UserContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class CreateOrderCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private EntityManagerInterface $commerceEntityManager,
        private LoggerInterface $logger,
        private UserContextInterface $userContext,
    ) {
    }

    public function __invoke(CreateOrderCommand $command): CommandResult
    {
        $user = $this->userContext->getUser();
        try {
            $order = new Order(
                $this->commerceEntityManager->getReference(Client::class, $user->getUserIdentifier()),
            );
            foreach ($command->dto->products as $product) {
                $order->addProduct(
                    $this->commerceEntityManager->getReference(Product::class, $product['id']),
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
