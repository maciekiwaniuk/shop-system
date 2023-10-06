<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Bus\CommandBus\CommandBusInterface;
use App\Application\Bus\QueryBus\QueryBusInterface;
use App\Application\Command\CreateProduct\CreateProductCommand;
use App\Application\DTO\Product\CreateProductDTO;
use App\Application\Query\FindProductBySlug\FindProductBySlugQuery;
use App\Application\Query\GetProducts\GetProductsQuery;
use App\Application\Voter\ProductsVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/products', name: 'products.')]
class ProductsController extends AbstractController
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBusInterface $queryBus
    ) {
    }

    #[Route('/get-all', methods: ['GET'])]
    #[IsGranted(ProductsVoter::GET_ALL)]
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

    #[Route('/new', methods: ['POST'])]
    #[IsGranted(ProductsVoter::NEW)]
    public function new(#[ValueResolver('create_product_dto')] CreateProductDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ], Response::HTTP_BAD_REQUEST);
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

    #[Route('/show/{slug}', methods: ['GET'])]
    #[IsGranted(ProductsVoter::SHOW)]
    public function show(string $slug): Response
    {
        $queryResult = $this->queryBus->handle(new FindProductBySlugQuery($slug));

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
}
