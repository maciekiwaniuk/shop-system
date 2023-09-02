<?php

declare(strict_types=1);

namespace App\Application\Query\GetOrders;

use App\Application\Bus\QueryBus\QueryBusInterface;
use App\Application\Query\QueryResultInterface;
use App\Infrastructure\Doctrine\Repository\OrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetOrdersQueryHandler
{
    public function __construct(
        protected readonly OrderRepository $orderRepository,
        protected readonly QueryBusInterface $queryBus
    ) {
    }

    public function __invoke(GetOrdersQuery $getOrdersQuery): QueryResultInterface
    {
        return $this->queryBus->handle($getOrdersQuery);
    }
}