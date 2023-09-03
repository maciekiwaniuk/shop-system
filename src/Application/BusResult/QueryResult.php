<?php

namespace App\Application\BusResult;

class QueryResult implements BusResultInterface
{
    public function __construct(
        public readonly ?bool $success = null,
        public readonly mixed $data = null
    ) {
    }
}