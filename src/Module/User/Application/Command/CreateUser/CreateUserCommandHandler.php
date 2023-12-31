<?php

declare(strict_types=1);

namespace App\Module\User\Application\Command\CreateUser;

use App\Module\User\Domain\Entity\User;
use App\Module\User\Infrastructure\Doctrine\Repository\UserRepository;
use App\Shared\Application\BusResult\CommandResult;
use App\Shared\Application\Command\CommandHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Throwable;

#[AsMessageHandler]
class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        protected readonly UserRepository $userRepository,
        protected readonly LoggerInterface $logger,
        protected readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(CreateUserCommand $command): CommandResult
    {
        try {
            $user = new User(
                email: $command->dto->email,
                password: $command->dto->password,
                name: $command->dto->name,
                surname: $command->dto->surname
            );
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPassword())
            );
            $this->userRepository->save($user, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
