<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindOrderByUuid;

use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Shared\Application\BusResult\QueryResult;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class FindOrderByUuidQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected readonly OrderRepositoryInterface $orderRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(FindOrderByUuidQuery $query): QueryResult
    {
        try {
            if (null === $order = $this->orderRepository->findByUuid($query->email)) {
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
            data: json_decode($this->serializer->serialize($order), true),
        );
    }
}
