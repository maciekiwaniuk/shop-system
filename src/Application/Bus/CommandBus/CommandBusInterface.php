<?php

namespace App\Application\Bus\CommandBus;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandInterface;

interface CommandBusInterface
{
    public function handle(CommandInterface $command): CommandResult;
}