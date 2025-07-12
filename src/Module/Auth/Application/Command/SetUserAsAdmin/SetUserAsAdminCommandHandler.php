<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\SetUserAsAdmin;

use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Enum\UserRole;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class SetUserAsAdminCommandHandler implements SyncCommandInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SetUserAsAdminCommand $command): CommandResult
    {
        try {
            $user = $this->entityManager->getReference(User::class, $command->user->getId());
            $user->setRoles(
                array_merge($command->user->getRoles(), [UserRole::ADMIN->value]),
            );
            $this->entityManager->flush();
        } catch (Throwable $exception) {
            $this->logger->error('Failed to set user as admin', [
                'user_email' => $command->user->getEmail(),
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_OK);
    }
}
