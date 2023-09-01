<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Query\GetOrders\GetOrdersQuery;
use App\Domain\DTO\Order\NewOrderDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/orders/', name: 'orders.')]
class OrdersController extends AbstractController
{
    public function __construct(
        protected readonly MessageBusInterface $bus,
        protected readonly SerializerInterface $serializer
    ) {
    }

    #[Route('/get-all', name: 'get-all', methods: ['GET'])]
    public function getAll(): Response
    {
        $orders = $this->bus->dispatch(new GetOrdersQuery());

        return $this->json([
            'data' => $this->serializer->encode($orders)
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(NewOrderDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ]);
        }
    }
}