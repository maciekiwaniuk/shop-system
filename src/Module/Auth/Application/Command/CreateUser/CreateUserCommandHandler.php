<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\CreateUser;

use App\Common\Application\Bus\AsyncCommandBus\AsyncCommandBusInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use App\Module\Auth\Application\Command\SendWelcomeEmail\SendWelcomeEmailCommand;
use App\Module\Auth\Application\DTO\Communication\SendWelcomeEmailDTO;
use App\Module\Auth\Application\Port\ClientFinderInterface;
use App\Module\Auth\Domain\Entity\User;
use App\Module\Auth\Domain\Event\UserRegisteredEvent;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\UserRegistrationDetails;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

#[AsMessageHandler]
readonly class CreateUserCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
        private UserPasswordHasherInterface $passwordHasher,
        private EventDispatcherInterface $eventDispatcher,
        private AsyncCommandBusInterface $asyncCommandBus,
        private ClientFinderInterface $clientFinder,
    ) {
    }

    public function __invoke(CreateUserCommand $command): CommandResult
    {
        try {
            $clientUuid = $this->lookForClientIdByEmailIfPreviouslyUsedCommerceWithoutAccount($command->dto->email);
            $user = $this->createUser($command, $clientUuid);
            $this->eventDispatcher->dispatch(
                new UserRegisteredEvent(
                    new UserRegistrationDetails(
                        id: $user->getId(),
                        email: $user->getEmail(),
                        name: $user->getName(),
                        surname: $user->getSurname(),
                    ),
                ),
            );
            $this->asyncCommandBus->handle(
                new SendWelcomeEmailCommand(SendWelcomeEmailDTO::fromEntity($user)),
            );
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }

    private function lookForClientIdByEmailIfPreviouslyUsedCommerceWithoutAccount(string $email): ?string
    {
        return $this->clientFinder->findClientIdByEmail($email);
    }

    private function createUser(CreateUserCommand $command, ?string $userUuid = null): User
    {
        if (isset($userUuid)) {
            $user = new User(
                email: $command->dto->email,
                password: $command->dto->password,
                name: $command->dto->name,
                surname: $command->dto->surname,
                id: $userUuid,
            );
        } else {
            $user = new User(
                email: $command->dto->email,
                password: $command->dto->password,
                name: $command->dto->name,
                surname: $command->dto->surname,
            );
        }
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->getPassword()),
        );
        $this->userRepository->save($user, true);
        return $user;
    }
}
