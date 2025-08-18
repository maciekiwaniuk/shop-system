<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateOrder;

use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use App\Common\Application\Security\UserContextInterface;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class CreateOrderCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ClientRepositoryInterface $clientRepository,
        private ProductRepositoryInterface $productRepository,
        private LoggerInterface $logger,
        private UserContextInterface $userContext,
    ) {
    }

    public function __invoke(CreateOrderCommand $command): CommandResult
    {
        $user = $this->userContext->getUser();
        try {
            $order = new Order(
                $this->clientRepository->getReference($user->getUserIdentifier()),
            );
            foreach ($command->dto->products as $product) {
                $order->addProduct(
                    $this->productRepository->getReference($product['id']),
                    $product['quantity'],
                    $product['pricePerPiece'],
                );
            }
            $this->orderRepository->save($order, true);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to create order', [
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
