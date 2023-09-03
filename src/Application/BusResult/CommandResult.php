<?php

namespace App\Application\BusResult;

class CommandResult implements BusResultInterface
{
    public function __construct(
        public readonly ?bool $success = null
    ) {
    }
}