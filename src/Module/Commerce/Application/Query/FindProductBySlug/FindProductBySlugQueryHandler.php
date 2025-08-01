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
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class FindProductBySlugQueryHandler implements QueryHandlerInterface
{
    private CacheProxyInterface $cache;

    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private JsonSerializerInterface $serializer,
        private LoggerInterface $logger,
        CacheCreatorInterface $cacheCreator,
    ) {
        $this->cache = $cacheCreator->create('query.products.findProductBySlugQuery.');
    }

    public function __invoke(FindProductBySlugQuery $query): QueryResult
    {
        try {
            $product = match (true) {
                $this->cache->exists($query->slug) => $this->cache->get($query->slug),
                default => $this->findBySlugAndCacheIt($query->slug)
            };
            if ($product === null) {
                return new QueryResult(
                    success: false,
                    statusCode: Response::HTTP_NOT_FOUND,
                );
            }
        } catch (Throwable $exception) {
            $this->logger->error('Failed to find product by slug', [
                'product_slug' => $query->slug,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new QueryResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: $product instanceof Product
                ? json_decode($this->serializer->serialize($product), true)
                : json_decode($product, true),
        );
    }

    private function findBySlugAndCacheIt(string $slug): ?Product
    {
        $product = $this->productRepository->findBySlug($slug);

        if ($product !== null) {
            $this->cache->set($slug, $this->serializer->serialize($product));
        }

        return $product;
    }
}
