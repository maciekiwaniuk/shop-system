<?php

namespace App\Application\Bus\QueryBus;

use App\Application\BusResult\QueryResult;
use App\Application\Query\QueryInterface;

interface QueryBusInterface
{
    public function handle(QueryInterface $query): QueryResult;
}