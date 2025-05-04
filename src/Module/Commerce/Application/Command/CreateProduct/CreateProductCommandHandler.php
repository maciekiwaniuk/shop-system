<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateProduct;

use App\Module\Commerce\Application\DTO\Communication\ProductDTO;
use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Commerce\Domain\Event\ProductCreatedEvent;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsMessageHandler]
readonly class CreateProductCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private LoggerInterface $logger,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateProductCommand $command): CommandResult
    {
        try {
            $product = new Product(
                name: $command->dto->name,
                price: $command->dto->price,
            );
            $this->productRepository->save($product, true);
            $this->eventDispatcher->dispatch(new ProductCreatedEvent(ProductDTO::fromEntity($product)));
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
