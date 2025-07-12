<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindOrderByUuid;

use App\Common\Domain\Serializer\JsonSerializerInterface;
use App\Module\Commerce\Domain\Repository\OrderRepositoryInterface;
use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class FindOrderByUuidQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private JsonSerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(FindOrderByUuidQuery $query): QueryResult
    {
        try {
            if (null === $order = $this->orderRepository->findByUuid($query->uuid)) {
                return new QueryResult(
                    success: false,
                    statusCode: Response::HTTP_NOT_FOUND,
                );
            }
        } catch (Throwable $exception) {
            $this->logger->error('Failed to find order by UUID', [
                'order_uuid' => $query->uuid,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new QueryResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: json_decode($this->serializer->serialize($order), true),
        );
    }
}
