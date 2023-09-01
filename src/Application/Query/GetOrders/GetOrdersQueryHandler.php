<?php

namespace App\Application\Query\GetOrders;

use App\Infrastructure\Doctrine\Repository\OrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class GetOrdersQueryHandler
{
    use HandleTrait;

    public function __construct(
        protected readonly OrderRepository $orderRepository,
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(GetOrdersQuery $getOrdersQuery): array
    {
        return $this->query($getOrdersQuery);
    }

    public function query(GetOrdersQuery $getOrdersQuery): array
    {
        return $this->handle();
    }
}