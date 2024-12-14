<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\CreateUser;

use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Throwable;

readonly class CreateUserCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected LoggerInterface $logger,
        protected UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(CreateUserCommand $command): CommandResult
    {
        try {
            $user = new User(
                email: $command->dto->email,
                password: $command->dto->password,
                name: $command->dto->name,
                surname: $command->dto->surname,
            );
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPassword()),
            );
            $this->userRepository->save($user, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
