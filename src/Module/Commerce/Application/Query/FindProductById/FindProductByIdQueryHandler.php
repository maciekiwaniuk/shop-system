<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindProductById;

use App\Module\Commerce\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use App\Common\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class FindProductByIdQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected JsonSerializer $serializer,
        protected LoggerInterface $logger,
    ) {
    }

    public function __invoke(FindProductByIdQuery $query): QueryResult
    {
        try {
            $product = $this->productRepository->findById($query->id);
            if ($product === null) {
                return new QueryResult(
                    success: false,
                    statusCode: Response::HTTP_NOT_FOUND,
                );
            }
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new QueryResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: json_decode($this->serializer->serialize($product), true),
        );
    }
}
