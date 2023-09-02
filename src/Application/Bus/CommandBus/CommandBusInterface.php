<?php

namespace App\Application\Bus\CommandBus;

use App\Application\Command\CommandResult;

interface CommandBusInterface
{
    public function handle(object $message): CommandResult;
}