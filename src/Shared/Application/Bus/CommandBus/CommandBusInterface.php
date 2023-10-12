<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus\CommandBus;

use App\Shared\Application\BusResult\CommandResult;
use App\Shared\Application\Command\CommandInterface;

interface CommandBusInterface
{
    public function handle(CommandInterface $command): CommandResult;
}
