<?php

declare(strict_types=1);

namespace App\Application\Query\GetOrders;

use App\Application\Bus\QueryBus\QueryBusInterface;
use App\Application\BusResult\QueryResult;
use App\Application\Query\QueryHandlerInterface;
use App\Application\Query\QueryInterface;
use App\Infrastructure\Doctrine\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetOrdersQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected readonly OrderRepository $orderRepository,
        protected readonly QueryBusInterface $queryBus
    ) {
    }

    public function __invoke(QueryInterface $query): QueryResult
    {
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: ['test']
        );
    }
}