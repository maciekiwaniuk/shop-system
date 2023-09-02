<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Bus\CommandBus\CommandBusInterface;
use App\Application\Command\CreateOrder\CreateOrderCommand;
use App\Domain\DTO\Order\CreateOrderDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/orders/', name: 'orders.')]
class OrdersController extends AbstractController
{
    public function __construct(
        protected readonly CommandBusInterface $bus,
        protected readonly SerializerInterface $serializer
    ) {
    }

//    #[Route('/get-all', name: 'get-all', methods: ['GET'])]
//    public function getAll(): Response
//    {
//        $queryResult = $this->bus->dispatch(new GetOrdersQuery());
//
//        return match () {
//
//        }
//
//        return $this->json([
//            'success' => true,
//            'data' => $this->serializer->encode($orders)
//        ], Response::HTTP_OK);
//    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(CreateOrderDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ]);
        }

        $commandResult = $this->bus->handle(new CreateOrderCommand($dto));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'data' => $this->serializer->encode($commandResult->data)
            ],
            default => [
                'success' => false
            ]
        };

        return $this->json($result);
    }
}