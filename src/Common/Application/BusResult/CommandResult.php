<?php

declare(strict_types=1);

namespace App\Common\Application\BusResult;

readonly class CommandResult implements BusResultInterface
{
    public function __construct(
        public bool $success,
        public int $statusCode,
    ) {
    }
}
