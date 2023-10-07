<?php

declare(strict_types=1);

namespace App\Application\Query\FindProductBySlug;

use App\Application\BusResult\QueryResult;
use App\Application\Query\QueryHandlerInterface;
use App\Domain\Entity\Product;
use App\Infrastructure\Cache\CacheCreator;
use App\Infrastructure\Cache\CacheProxy;
use App\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class FindProductBySlugQueryHandler implements QueryHandlerInterface
{
    protected readonly CacheProxy $cache;

    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger,
        CacheCreator $cacheCreator
    ) {
        $this->cache = $cacheCreator->create('query.products.findProductBySlugQuery.');
    }

    public function __invoke(FindProductBySlugQuery $query): QueryResult
    {
        try {
            $product = match (true) {
                $this->cache->exists($query->slug) => $this->cache->get($query->slug),
                default => $this->findBySlugReturnAndCache($query->slug)
            };
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new QueryResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: $product instanceof Product
                ? json_decode($this->serializer->serialize($product), true)
                : json_decode($product, true)
        );
    }

    protected function findBySlugReturnAndCache(string $uuid): Product
    {
        $product = $this->productRepository->findBySlug($uuid);

        $this->cache->set($uuid, $this->serializer->serialize($product));

        return $product;
    }
}
