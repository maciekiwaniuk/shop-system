<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\FindClientByEmail;

use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use App\Common\Domain\Serializer\JsonSerializerInterface;
use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class FindClientByEmailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private JsonSerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(FindClientByEmailQuery $query): QueryResult
    {
        try {
            if (null === $client = $this->clientRepository->findClientByEmail($query->email)) {
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
            data: json_decode($this->serializer->serialize($client), true),
        );
    }
}
