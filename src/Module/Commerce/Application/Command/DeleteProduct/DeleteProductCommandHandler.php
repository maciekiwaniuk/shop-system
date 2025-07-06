<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\DeleteProduct;

use App\Common\Domain\Cache\CacheCreatorInterface;
use App\Common\Domain\Cache\CacheProxyInterface;
use App\Module\Commerce\Domain\Event\ProductDeletedEvent;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsMessageHandler]
readonly class DeleteProductCommandHandler implements SyncCommandHandlerInterface
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

    public function __invoke(DeleteProductCommand $command): CommandResult
    {
        try {
            $this->cache->delByKeys([$command->product->getSlug()]);

            if (!$this->productRepository->softDelete($command->product)) {
                return new CommandResult(success: false, statusCode: Response::HTTP_NOT_FOUND);
            }

            $this->eventDispatcher->dispatch(new ProductDeletedEvent($command->product->getId()));
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_ACCEPTED);
    }
}
