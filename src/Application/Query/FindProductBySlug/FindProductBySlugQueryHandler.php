<?php

declare(strict_types=1);

namespace App\Application\Query\FindProductBySlug;

use App\Application\BusResult\QueryResult;
use App\Application\Query\QueryHandlerInterface;
use App\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class FindProductBySlugQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(FindProductBySlugQuery $query): QueryResult
    {
        try {
            $product = $this->productRepository->findBySlug($query->uuid);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new QueryResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: json_decode($this->serializer->serialize($product), true)
        );
    }
}
