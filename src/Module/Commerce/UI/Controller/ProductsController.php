<?php

declare(strict_types=1);

namespace App\Module\Commerce\UI\Controller;

use App\Module\Commerce\Application\SyncCommand\CreateProduct\CreateProductCommand;
use App\Module\Commerce\Application\SyncCommand\DeleteProduct\DeleteProductCommand;
use App\Module\Commerce\Application\SyncCommand\UpdateProduct\UpdateProductCommand;
use App\Module\Commerce\Application\DTO\CreateProductDTO;
use App\Module\Commerce\Application\DTO\UpdateProductDTO;
use App\Module\Commerce\Application\Query\FindProductById\FindProductByIdQuery;
use App\Module\Commerce\Application\Query\FindProductBySlug\FindProductBySlugQuery;
use App\Module\Commerce\Application\Query\GetPaginatedProducts\GetPaginatedProductsQuery;
use App\Module\Commerce\Application\Voter\ProductsVoter;
use App\Module\Commerce\Domain\Entity\Product;
use App\Common\Application\Bus\CommandBus\CommandBusInterface;
use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Common\Application\DTO\PaginationIdDTO;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
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
        protected readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return paginated products',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Product::class, groups: ['default'])),
                ),
            ],
        ),
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Set offset (ID) for pagination',
        schema: new OA\Schema(type: 'int'),
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Set limit for pagination',
        schema: new OA\Schema(type: 'int'),
    )]
    #[Route('/get-paginated', methods: [Request::METHOD_GET])]
    #[IsGranted(ProductsVoter::GET_PAGINATED)]
    public function getPaginated(#[ValueResolver('get_paginated_products')] PaginationIdDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $queryResult = $this->queryBus->handle(new GetPaginatedProductsQuery($dto->offset, $dto->limit));

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $queryResult->data,
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while getting paginated products.',
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(property: 'message', type: 'string'),
            ],
        ),
    )]
    #[OA\RequestBody(content: new Model(type: CreateProductDTO::class, groups: ['default']))]
    #[Route('/create', methods: [Request::METHOD_POST])]
    #[IsGranted(ProductsVoter::CREATE)]
    public function create(#[ValueResolver('create_product_dto')] CreateProductDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $commandResult = $this->commandBus->handle(new CreateProductCommand($dto));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully created product.',
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while creating product.',
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Show product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(
                    property: 'data',
                    ref: new Model(type: Product::class, groups: ['default']),
                    type: 'object',
                ),
            ],
        ),
    )]
    #[Route('/show/{slug}', methods: [Request::METHOD_GET])]
    #[IsGranted(ProductsVoter::SHOW)]
    public function show(string $slug): Response
    {
        $queryResult = $this->queryBus->handle(new FindProductBySlugQuery($slug));

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $queryResult->data,
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while showing product.',
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Update product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(property: 'message', type: 'string'),
            ],
        ),
    )]
    #[OA\RequestBody(content: new Model(type: UpdateProductDTO::class, groups: ['default']))]
    #[Route('/update/{id}', methods: [Request::METHOD_PUT])]
    #[IsGranted(ProductsVoter::UPDATE)]
    public function update(
        #[ValueResolver('update_product_dto')] UpdateProductDTO $dto,
        int $id,
    ): Response {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $queryResult = $this->queryBus->handle(new FindProductByIdQuery($id));
        if ($queryResult->data !== null) {
            $product = $this->entityManager->getReference(Product::class, $queryResult->data['id']);
            $commandResult = $this->commandBus->handle(new UpdateProductCommand($product, $dto));
        }

        $result = match (true) {
            $queryResult->data === null => [
                'success' => false,
                'message' => 'Update failed. Could not find product with given id.',
            ],
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully updated product.',
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while updating product.',
            ]
        };
        return $this->json($result, isset($commandResult) ? $commandResult->statusCode : $queryResult->statusCode);
    }

    #[OA\Response(
        response: Response::HTTP_ACCEPTED,
        description: 'Soft delete product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(property: 'message', type: 'string'),
            ],
        ),
    )]
    #[Route('/delete/{id}', methods: [Request::METHOD_DELETE])]
    #[IsGranted(ProductsVoter::DELETE)]
    public function delete(int $id): Response
    {
        $queryResult = $this->queryBus->handle(new FindProductByIdQuery($id));
        if ($queryResult->data !== null) {
            $product = $this->entityManager->getReference(Product::class, $queryResult->data['id']);
            $commandResult = $this->commandBus->handle(new DeleteProductCommand($product));
        }

        $result = match (true) {
            $queryResult->data === null => [
                'success' => false,
                'message' => 'Deletion failed. Could not find product with given id.',
            ],
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully deleted product.',
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while deleting product.',
            ]
        };
        return $this->json($result, isset($commandResult) ? $commandResult->statusCode : $queryResult->statusCode);
    }
}
