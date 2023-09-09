<?php

namespace App\Application\Query;

use App\Application\BusResult\QueryResult;

interface QueryHandlerInterface
{
    public function __invoke(QueryInterface $query): QueryResult;
}