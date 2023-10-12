<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus\QueryBus;

use App\Shared\Application\BusResult\QueryResult;
use App\Shared\Application\Query\QueryInterface;

interface QueryBusInterface
{
    public function handle(QueryInterface $query): QueryResult;
}
