<?php

namespace App\Application\Command;

use App\Application\BusResult\CommandResult;

interface CommandHandlerInterface
{
    public function __invoke(CommandInterface $command): CommandResult;
}