<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\EventListener;

use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Module\Auth\Domain\Event\UserRegisteredEvent;
use App\Module\Commerce\Application\Command\CreateClient\CreateClientCommand;
use App\Module\Commerce\Application\DTO\Communication\CreateClientDTO;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: UserRegisteredEvent::class)]
readonly class CreateClientOnRegistrationListener
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    public function __invoke(UserRegisteredEvent $event): void
    {
        $this->syncCommandBus->handle(
            new CreateClientCommand(
                new CreateClientDTO(
                    $event->dto->id,
                    $event->dto->email,
                    $event->dto->name,
                    $event->dto->surname,
                ),
            ),
        );
    }
}
