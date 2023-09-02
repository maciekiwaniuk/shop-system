<?php

declare(strict_types=1);

namespace App\Application\Query;

class QueryResult implements QueryResultInterface
{
    public function __construct(
        public readonly ?bool $success = null,
        public readonly ?bool $failure = null,
        public readonly mixed $data = null
    ) {
    }
}
