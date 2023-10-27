<?php

declare(strict_types=1);

namespace App\Module\Product\UI\Controller;

use App\Module\Product\Application\Command\CreateProduct\CreateProductCommand;
use App\Module\Product\Application\Command\DeleteProduct\DeleteProductCommand;
use App\Module\Product\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Module\Product\Application\DTO\CreateProductDTO;
use App\Module\Product\Application\DTO\UpdateProductDTO;
use App\Module\Product\Application\Query\FindProductBySlug\FindProductBySlugQuery;
use App\Module\Product\Application\Query\FindProductByUuid\FindProductByUuidQuery;
use App\Module\Product\Application\Query\GetProducts\GetProductsQuery;
use App\Module\Product\Application\Voter\ProductsVoter;
use App\Shared\Application\Bus\CommandBus\CommandBusInterface;
use App\Shared\Application\Bus\QueryBus\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/products')]
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

    #[Route('/create', methods: ['POST'])]
    #[IsGranted(ProductsVoter::CREATE)]
    public function create(#[ValueResolver('create_product_dto')] CreateProductDTO $dto): Response
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

    #[Route('/update/{uuid}', methods: ['PUT'])]
    #[IsGranted(ProductsVoter::UPDATE)]
    public function update(
        #[ValueResolver('update_product_dto')] UpdateProductDTO $dto,
        string $uuid
    ): Response {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $queryResult = $this->queryBus->handle(new FindProductByUuidQuery($uuid));
        $commandResult = $this->commandBus->handle(new UpdateProductCommand($queryResult->data, $dto));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully updated product.'
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while updating product.'
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }

    #[Route('/delete/{uuid}', methods: ['DELETE'])]
    #[IsGranted(ProductsVoter::DELETE)]
    public function delete(string $uuid): Response
    {
        $queryResult = $this->queryBus->handle(new FindProductByUuidQuery($uuid));
        $commandResult = $this->commandBus->handle(new DeleteProductCommand($queryResult->data));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully deleted product.'
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while deleting product.'
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }
}
