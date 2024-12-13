<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\SyncCommandBus;

use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\SyncCommand\SyncCommandInterface;

interface SyncCommandBusInterface
{
    public function handle(SyncCommandInterface $command): CommandResult;
}
