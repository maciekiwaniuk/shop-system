<?php

declare(strict_types=1);

namespace App\Module\Commerce\Interface\Controller;

use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Common\Application\DTO\PaginationIdDTO;
use App\Module\Commerce\Application\Command\CreateProduct\CreateProductCommand;
use App\Module\Commerce\Application\Command\DeleteProduct\DeleteProductCommand;
use App\Module\Commerce\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Module\Commerce\Application\DTO\Validation\CreateProductDTO;
use App\Module\Commerce\Application\DTO\Validation\SearchProductsDTO;
use App\Module\Commerce\Application\DTO\Validation\UpdateProductDTO;
use App\Module\Commerce\Application\Query\FindProductById\FindProductByIdQuery;
use App\Module\Commerce\Application\Query\FindProductBySlug\FindProductBySlugQuery;
use App\Module\Commerce\Application\Query\GetPaginatedProducts\GetPaginatedProductsQuery;
use App\Module\Commerce\Application\Query\SearchProductsByPhrase\SearchProductsByPhraseQuery;
use App\Module\Commerce\Application\Voter\ProductsVoter;
use App\Module\Commerce\Domain\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly SyncCommandBusInterface $syncCommandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly EntityManagerInterface $commerceEntityManager,
    ) {
    }

    #[Route('/get-paginated', methods: [Request::METHOD_GET])]
    #[IsGranted(ProductsVoter::GET_PAGINATED)]
    public function getPaginated(#[ValueResolver('get_paginated_products_dto')] PaginationIdDTO $dto): Response
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

        $commandResult = $this->syncCommandBus->handle(new CreateProductCommand($dto));

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
            $product = $this->commerceEntityManager->getReference(Product::class, $queryResult->data['id']);
            $commandResult = $this->syncCommandBus->handle(new UpdateProductCommand($product, $dto));
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

    #[Route('/delete/{id}', methods: [Request::METHOD_DELETE])]
    #[IsGranted(ProductsVoter::DELETE)]
    public function delete(int $id): Response
    {
        $queryResult = $this->queryBus->handle(new FindProductByIdQuery($id));
        if ($queryResult->data !== null) {
            $product = $this->commerceEntityManager->getReference(Product::class, $queryResult->data['id']);
            $commandResult = $this->syncCommandBus->handle(new DeleteProductCommand($product));
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

    // TODO: Tests
    #[Route('/search', methods: [Request::METHOD_GET])]
    #[IsGranted(ProductsVoter::SEARCH)]
    public function search(#[ValueResolver('search_products_dto')] SearchProductsDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $queryResult = $this->queryBus->handle(new SearchProductsByPhraseQuery($dto->phrase));

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $queryResult->data,
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while searching for products by phrase.',
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }
}
