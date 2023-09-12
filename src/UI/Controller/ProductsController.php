<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Bus\CommandBus\CommandBusInterface;
use App\Application\Bus\QueryBus\QueryBusInterface;
use App\Application\Command\CreateOrder\CreateOrderCommand;
use App\Application\Command\CreateProduct\CreateProductCommand;
use App\Application\Query\GetProducts\GetProductsQuery;
use App\Domain\DTO\Order\CreateOrderDTO;
use App\Domain\DTO\Product\CreateProductDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/products', name: 'products.')]
class ProductsController extends AbstractController
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBusInterface $queryBus
    ) {
    }

    #[Route('/get-all', name: 'get-all', methods: ['GET'])]
    public function getAll(): Response
    {
        $queryResult = $this->queryBus->handle(new GetProductsQuery());

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $queryResult->data
            ],
            default => [
                'success' => false
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(CreateProductDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ]);
        }

        $commandResult = $this->commandBus->handle(new CreateProductCommand($dto));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully created product.'
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while creating product.'
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }
}