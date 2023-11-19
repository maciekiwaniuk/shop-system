<?php

declare(strict_types=1);

namespace App\Module\Product\UI\Controller;

use App\Module\Product\Application\Command\CreateProduct\CreateProductCommand;
use App\Module\Product\Application\Command\DeleteProduct\DeleteProductCommand;
use App\Module\Product\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Module\Product\Application\DTO\CreateProductDTO;
use App\Module\Product\Application\DTO\UpdateProductDTO;
use App\Module\Product\Application\Query\FindProductBySlug\FindProductBySlugQuery;
use App\Module\Product\Application\Query\FindProductById\FindProductByIdQuery;
use App\Module\Product\Application\Query\GetPaginatedProducts\GetPaginatedProductsQuery;
use App\Module\Product\Application\Voter\ProductsVoter;
use App\Module\Product\Domain\Entity\Product;
use App\Shared\Application\Bus\CommandBus\CommandBusInterface;
use App\Shared\Application\Bus\QueryBus\QueryBusInterface;
use App\Shared\Application\DTO\PaginationDTO;
use App\Shared\Infrastructure\Serializer\JsonSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/products')]
class ProductsController extends AbstractController
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBusInterface $queryBus,
        protected readonly JsonSerializer $serializer
    ) {
    }

    #[Route('/get-paginated', methods: [Request::METHOD_GET])]
    #[IsGranted(ProductsVoter::GET_PAGINATED)]
    public function getPaginated(#[ValueResolver('get_paginated_products')] PaginationDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $queryResult = $this->queryBus->handle(new GetPaginatedProductsQuery($dto->offset, $dto->limit));

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

    #[Route('/create', methods: [Request::METHOD_POST])]
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

    #[Route('/show/{slug}', methods: [Request::METHOD_GET])]
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

    #[Route('/update/{id}', methods: [Request::METHOD_PUT])]
    #[IsGranted(ProductsVoter::UPDATE)]
    public function update(
        #[ValueResolver('update_product_dto')] UpdateProductDTO $dto,
        int $id
    ): Response {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $queryResult = $this->queryBus->handle(new FindProductByIdQuery($id));
        if ($queryResult->data !== null) {
            $product = $this->serializer->deserialize(json_encode($queryResult->data), Product::class);
            $commandResult = $this->commandBus->handle(new UpdateProductCommand($product, $dto));
        }

        $result = match (true) {
            $queryResult->data === null => [
                'success' => false,
                'message' => 'Update failed. Could not find product with given id.'
            ],
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully updated product.'
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while updating product.'
            ]
        };
        return $this->json($result, isset($commandResult) ? $commandResult->statusCode : $queryResult->statusCode);
    }

    #[Route('/delete/{id}', methods: [Request::METHOD_DELETE])]
    #[IsGranted(ProductsVoter::DELETE)]
    public function delete(int $id): Response
    {
        $queryResult = $this->queryBus->handle(new FindProductByIdQuery($id));
        if ($queryResult->data !== null) {
            $product = $this->serializer->deserialize(json_encode($queryResult->data), Product::class);
            $commandResult = $this->commandBus->handle(new DeleteProductCommand($product));
        }

        $result = match (true) {
            $queryResult->data === null => [
                'success' => false,
                'message' => 'Deletion failed. Could not find product with given id.'
            ],
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully deleted product.'
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while deleting product.'
            ]
        };
        return $this->json($result, isset($commandResult) ? $commandResult->statusCode : $queryResult->statusCode);
    }
}
