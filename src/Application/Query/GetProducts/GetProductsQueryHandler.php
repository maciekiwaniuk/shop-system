<?php

namespace App\Application\Query\GetProducts;

use App\Application\BusResult\QueryResult;
use App\Application\Query\QueryHandlerInterface;
use App\Infrastructure\Cache\RedisCacheAdapter;
use App\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class GetProductsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger,
        protected readonly RedisCacheAdapter $cache
    ) {
    }

    public function __invoke(GetProductsQuery $query): QueryResult
    {
        try {
            $products = $this->productRepository->findAll();
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
            data: json_decode($this->serializer->serialize($products), true)
        );
    }
}