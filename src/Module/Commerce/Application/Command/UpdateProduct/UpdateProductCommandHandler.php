<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\UpdateProduct;

use App\Common\Domain\Cache\CacheCreatorInterface;
use App\Common\Domain\Cache\CacheProxyInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandInterface;
use App\Module\Commerce\Domain\Event\ProductUpdatedEvent;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsMessageHandler]
readonly class UpdateProductCommandHandler implements SyncCommandInterface
{
    private CacheProxyInterface $cache;

    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private LoggerInterface $logger,
        private EventDispatcherInterface $eventDispatcher,
        CacheCreatorInterface $cacheCreator,
    ) {
        $this->cache = $cacheCreator->create('query.products.findProductBySlugQuery.');
    }

    public function __invoke(UpdateProductCommand $command): CommandResult
    {
        try {
            $this->cache->delByKeys([$command->product->getSlug()]);

            $product = $this->productRepository->getReference($command->product->getId());
            $product
                ->setName($command->dto->name)
                ->setPrice($command->dto->price);
            $this->productRepository->save($product, true);
            $this->eventDispatcher->dispatch(new ProductUpdatedEvent($product));
        } catch (Throwable $exception) {
            $this->logger->error('Failed to update product', [
                'product_id' => $command->product->getId(),
                'new_name' => $command->dto->name,
                'new_price' => $command->dto->price,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_OK);
    }
}
