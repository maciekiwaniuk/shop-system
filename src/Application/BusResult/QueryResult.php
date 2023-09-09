<?php

declare(strict_types=1);

namespace App\Application\BusResult;

class QueryResult implements BusResultInterface
{
    public function __construct(
        public readonly bool $success,
        public readonly int $statusCode,
        public readonly mixed $data = null
    ) {
    }
}