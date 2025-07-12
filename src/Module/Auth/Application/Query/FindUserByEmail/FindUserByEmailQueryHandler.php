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
        private UserRepositoryInterface $userRepository,
        private JsonSerializerInterface $serializer,
        private LoggerInterface $logger,
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
        } catch (Throwable $exception) {
            $this->logger->error('Failed to find user by email', [
                'email' => $query->email,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new QueryResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: json_decode($this->serializer->serialize($user), true),
        );
    }
}
