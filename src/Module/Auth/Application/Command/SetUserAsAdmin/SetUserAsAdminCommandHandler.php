<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\SetUserAsAdmin;

use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Enum\UserRole;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\Command\CommandInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler(fromTransport: 'sync')]
readonly class SetUserAsAdminCommandHandler implements CommandInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger,
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
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_OK);
    }
}
