<?php

declare(strict_types=1);

namespace App\Shared\Application\BusResult;

class CommandResult implements BusResultInterface
{
    public function __construct(
        public readonly bool $success,
        public readonly int $statusCode
    ) {
    }
}
