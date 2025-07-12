<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\GetPaginatedProducts;

use App\Common\Domain\Serializer\JsonSerializerInterface;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class GetPaginatedProductsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private JsonSerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(GetPaginatedProductsQuery $query): QueryResult
    {
        try {
            $products = $this->productRepository->getPaginatedById($query->offset, $query->limit);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to get paginated products', [
                'offset' => $query->offset,
                'limit' => $query->limit,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new QueryResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: json_decode($this->serializer->serialize($products), true),
        );
    }
}
