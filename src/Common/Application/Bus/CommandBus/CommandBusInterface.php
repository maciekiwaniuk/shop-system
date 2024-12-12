<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\CommandBus;

use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandInterface;

interface CommandBusInterface
{
    public function handle(SyncCommandInterface $command): CommandResult;
}
