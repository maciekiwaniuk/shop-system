<?php

declare(strict_types=1);

namespace App\Module\Product\Application\Query\GetPaginatedProducts;

use App\Module\Product\Domain\Repository\ProductRepositoryInterface;
use App\Shared\Application\BusResult\QueryResult;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class GetPaginatedProductsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected readonly ProductRepositoryInterface $productRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(GetPaginatedProductsQuery $query): QueryResult
    {
        try {
            $products = $this->productRepository->getPaginatedById($query->offset, $query->limit);
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
            data: json_decode($this->serializer->serialize($products), true),
        );
    }
}
