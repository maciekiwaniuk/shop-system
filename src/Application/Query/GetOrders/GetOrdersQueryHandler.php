<?php

declare(strict_types=1);

namespace App\Application\Query\GetOrders;

use App\Application\BusResult\QueryResult;
use App\Application\Query\QueryHandlerInterface;
use App\Application\Query\QueryInterface;
use App\Infrastructure\Doctrine\Repository\OrderRepository;
use App\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class GetOrdersQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected readonly OrderRepository $orderRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(QueryInterface $query): QueryResult
    {
        try {
            $orders = $this->orderRepository->findAll();
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
            data: $this->serializer->serialize($orders)
        );
    }
}