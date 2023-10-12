<?php

declare(strict_types=1);

namespace App\Module\User\Application\Query\FindUserByEmail;

use App\Module\User\Domain\Repository\UserRepositoryInterface;
use App\Shared\Application\BusResult\QueryResult;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Serializer\JsonSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class FindUserByEmailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected readonly UserRepositoryInterface $userRepository,
        protected readonly JsonSerializer $serializer,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(FindUserByEmailQuery $query): QueryResult
    {
        try {
            $user = $this->userRepository->findUserByEmail($query->email);
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
            data: json_decode($this->serializer->serialize($user), true)
        );
    }
}
