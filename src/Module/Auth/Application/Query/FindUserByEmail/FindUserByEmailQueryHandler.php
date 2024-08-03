<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query\FindUserByEmail;

use App\Common\Domain\Serializer\JsonSerializerInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class FindUserByEmailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected JsonSerializerInterface $serializer,
        protected LoggerInterface $logger,
    ) {
    }

    public function __invoke(FindUserByEmailQuery $query): QueryResult
    {
        try {
            $user = $this->userRepository->findUserByEmail($query->email);
            if ($user === null) {
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
            data: json_decode($this->serializer->serialize($user), true),
        );
    }
}
