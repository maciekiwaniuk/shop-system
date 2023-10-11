<?php

declare(strict_types=1);

namespace App\Application\Query\FindProductByUuid;

use App\Application\BusResult\QueryResult;
use App\Application\Query\QueryHandlerInterface;
use App\Domain\Entity\Product;
use App\Infrastructure\Cache\CacheProxy;
use App\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class FindProductByUuidQueryHandler implements QueryHandlerInterface
{
    protected readonly CacheProxy $cache;

    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(FindProductByUuidQuery $query): QueryResult
    {
        try {
            $product = $this->productRepository->findByUuid($query->uuid);
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
            data: json_decode($this->serializer->serialize($product), true)
        );
    }
}
