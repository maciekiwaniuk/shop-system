<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\AsyncCommandBus;

use App\Common\Application\AsyncCommand\AsyncCommandInterface;

interface AsyncCommandBusInterface
{
    public function handle(AsyncCommandInterface $command): void;
}
