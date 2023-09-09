<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Bus\CommandBus\CommandBusInterface;
use App\Application\Bus\QueryBus\QueryBus;
use App\Application\Bus\QueryBus\QueryBusInterface;
use App\Application\Command\CreateOrder\CreateOrderCommand;
use App\Application\Query\GetOrders\GetOrdersQuery;
use App\Domain\DTO\Order\CreateOrderDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/orders', name: 'orders.')]
class OrdersController extends AbstractController
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBus $queryBus,
        protected readonly SerializerInterface $serializer,
        protected readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route('/get-all', name: 'get-all', methods: ['GET'])]
    public function getAll(): Response
    {
        $queryResult = $this->queryBus->handle(new GetOrdersQuery());

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $this->serializer->encode($queryResult->data, 'json')
            ],
            default => [
                'success' => false
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(CreateOrderDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ]);
        }

        $commandResult = $this->commandBus->handle(new CreateOrderCommand($dto));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully created order.'
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while creating order.'
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }
}