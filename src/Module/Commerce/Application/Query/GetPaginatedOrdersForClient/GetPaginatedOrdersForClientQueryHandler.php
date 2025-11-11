<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\GetPaginatedOrdersForClient;

use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use App\Common\Domain\Serializer\JsonSerializerInterface;
use App\Common\Application\Security\UserContextInterface;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class GetPaginatedOrdersForClientQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private JsonSerializerInterface $serializer,
        private UserContextInterface $userContext,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(GetPaginatedOrdersForClientQuery $query): QueryResult
    {
        try {
            $clientId = $this->userContext->getUserIdentifier();
            $orders = $this->orderRepository->getPaginatedByClientId(
                $clientId,
                $query->cursor,
                $query->limit ?? 10
            );
        } catch (Throwable $exception) {
            $this->logger->error('Failed to get paginated orders for client', [
                'cursor' => $query->cursor,
                'limit' => $query->limit,
                'clientId' => $this->userContext->getUserIdentifier(),
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new QueryResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: json_decode($this->serializer->serialize($orders), true),
        );
    }
}
