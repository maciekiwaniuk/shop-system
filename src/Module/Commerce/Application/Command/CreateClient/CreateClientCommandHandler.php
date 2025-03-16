<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateClient;

use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandHandlerInterface;
use App\Module\Commerce\Domain\Entity\Client;
use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class CreateClientCommandHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateClientCommand $command): CommandResult
    {
        try {
            $client = new Client(
                $command->dto->id,
                $command->dto->email,
                $command->dto->name,
                $command->dto->surname,
            );
            $this->clientRepository->save($client, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
