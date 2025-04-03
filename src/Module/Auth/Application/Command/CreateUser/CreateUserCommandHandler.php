<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\CreateUser;

use App\Common\Application\Bus\AsyncCommandBus\AsyncCommandBusInterface;
use App\Module\Auth\Application\Command\SendWelcomeEmail\SendWelcomeEmailCommand;
use App\Module\Auth\Application\DTO\Communication\SendWelcomeEmailDTO;
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
        private HttpClientInterface $httpClient,
    ) {
    }

    public function __invoke(CreateUserCommand $command): CommandResult
    {
        try {
            $userUuid = $this->checkIfClientExists($command->dto->email);
            $user = $this->createUser($command, $userUuid);

            $this->eventDispatcher->dispatch(
                new UserRegisteredEvent(UserRegisteredDTO::fromEntity($user))
            );
            $this->asyncCommandBus->handle(
                new SendWelcomeEmailCommand(SendWelcomeEmailDTO::fromEntity($user))
            );
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }

    private function checkIfClientExists(string $email): ?string
    {
        try {
            $response = $this->httpClient->request('GET', 'api/v1/client/exists', [
                'query' => ['email' => $email],
            ]);

            $data = json_decode($response->getContent(), true);

            if ($data['success'] && $data['data']['exists']) {
                return $data['data']['id'];
            }
        } catch (Throwable $throwable) {
            $this->logger->error('Failed to check client existence: ' . $throwable->getMessage());
        }

        return null;
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
