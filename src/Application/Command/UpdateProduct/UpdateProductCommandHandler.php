<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateProduct;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandInterface;
use App\Infrastructure\Cache\CacheCreator;
use App\Infrastructure\Cache\CacheProxy;
use App\Infrastructure\Doctrine\Repository\ProductRepository;
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
            $this->cache->del([$command->product->getId()]);
            
            $command->product
                ->setName($command->dto->name)
                ->setPrice($command->dto->price);
            $this->productRepository->save($command->product, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_OK);
    }
}
