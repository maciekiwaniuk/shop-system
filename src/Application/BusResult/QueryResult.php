<?php

declare(strict_types=1);

namespace App\Application\BusResult;

class QueryResult implements BusResultInterface
{
    public function __construct(
        public readonly ?bool $success = null,
        public readonly ?int $statusCode = null,
        public readonly mixed $data = null
    ) {
    }
}