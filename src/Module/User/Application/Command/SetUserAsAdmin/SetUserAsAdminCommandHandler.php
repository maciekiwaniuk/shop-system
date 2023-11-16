<?php

declare(strict_types=1);

namespace App\Module\User\Application\Command\SetUserAsAdmin;

use App\Module\User\Domain\Enum\UserRole;
use App\Module\User\Infrastructure\Doctrine\Repository\UserRepository;
use App\Shared\Application\BusResult\CommandResult;
use App\Shared\Application\Command\CommandInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class SetUserAsAdminCommandHandler implements CommandInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(SetUserAsAdminCommand $command): CommandResult
    {
        try {
            $command->user->setRoles(
                array_merge($command->user->getRoles(), [UserRole::ADMIN->value])
            );
            var_dump($command->user->getRoles());
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true,statusCode: Response::HTTP_OK);
    }
}
