<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindProductBySlug;

use App\Common\Domain\Cache\CacheCreatorInterface;
use App\Common\Domain\Cache\CacheProxyInterface;
use App\Common\Domain\Serializer\JsonSerializerInterface;
use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

readonly class FindProductBySlugQueryHandler implements QueryHandlerInterface
{
    protected CacheProxyInterface $cache;

    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected JsonSerializerInterface $serializer,
        protected LoggerInterface $logger,
        CacheCreatorInterface $cacheCreator,
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
            if ($product === null) {
                return new QueryResult(
                    success: false,
                    statusCode: Response::HTTP_NOT_FOUND,
                );
            }
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new QueryResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: $product instanceof Product
                ? json_decode($this->serializer->serialize($product), true)
                : json_decode($product, true),
        );
    }

    protected function findBySlugReturnAndCache(string $slug): ?Product
    {
        $product = $this->productRepository->findBySlug($slug);

        if ($product !== null) {
            $this->cache->set($slug, $this->serializer->serialize($product));
        }

        return $product;
    }
}
