<?php

declare(strict_types=1);

namespace App\Module\Product\Application\Command\UpdateProduct;

use App\Module\Product\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Shared\Application\BusResult\CommandResult;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Cache\CacheCreator;
use App\Shared\Infrastructure\Cache\CacheProxy;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class UpdateProductCommandHandler implements CommandInterface
{
    protected readonly CacheProxy $cache;

    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly LoggerInterface $logger,
        CacheCreator $cacheCreator
    ) {
        $this->cache = $cacheCreator->create('query.products.findProductBySlugQuery.');
    }

    public function __invoke(UpdateProductCommand $command): CommandResult
    {
        try {
            $this->cache->delByKeys([$command->product->getSlug()]);

            $updated = $this->productRepository->update(
                product: $command->product,
                name: $command->dto->name,
                price: $command->dto->price
            );
            if (!$updated) {
                return new CommandResult(success: false, statusCode: Response::HTTP_NOT_FOUND);
            }
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_OK);
    }
}
