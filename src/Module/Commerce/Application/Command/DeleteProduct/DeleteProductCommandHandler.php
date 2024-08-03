<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\DeleteProduct;

use App\Module\Commerce\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\Command\CommandHandlerInterface;
use App\Common\Infrastructure\Cache\CacheCreator;
use App\Common\Infrastructure\Cache\CacheProxy;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class DeleteProductCommandHandler implements CommandHandlerInterface
{
    protected readonly CacheProxy $cache;

    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly LoggerInterface $logger,
        CacheCreator $cacheCreator,
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
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_ACCEPTED);
    }
}
