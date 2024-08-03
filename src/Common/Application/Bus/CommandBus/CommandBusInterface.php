<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\CommandBus;

use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\Command\CommandInterface;

interface CommandBusInterface
{
    public function handle(CommandInterface $command): CommandResult;
}
