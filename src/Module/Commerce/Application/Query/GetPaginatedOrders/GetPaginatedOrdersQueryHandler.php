<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\GetPaginatedOrders;

use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Shared\Application\BusResult\QueryResult;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class GetPaginatedOrdersQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected readonly OrderRepositoryInterface $orderRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(GetPaginatedOrdersQuery $query): QueryResult
    {
        try {
            $orders = $this->orderRepository->getPaginatedByUuid($query->cursor, $query->limit);
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
            data: json_decode($this->serializer->serialize($orders), true),
        );
    }
}
