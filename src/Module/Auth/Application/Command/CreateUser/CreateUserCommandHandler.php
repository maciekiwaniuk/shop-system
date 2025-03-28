<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\CreateUser;

use App\Common\Application\Bus\AsyncCommandBus\AsyncCommandBusInterface;
use App\Module\Auth\Application\Command\SendWelcomeEmail\SendWelcomeEmailCommand;
use App\Module\Auth\Application\DTO\Communication\UserRegisteredDTO;
use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Event\UserRegisteredEvent;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsMessageHandler]
readonly class CreateUserCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
        private UserPasswordHasherInterface $passwordHasher,
        private EventDispatcherInterface $eventDispatcher,
        private AsyncCommandBusInterface $asyncCommandBus
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
            $this->eventDispatcher->dispatch(
                new UserRegisteredEvent(UserRegisteredDTO::fromEntity($user))
            );
            $this->asyncCommandBus->handle(SendWelcomeEmailCommand::class);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
