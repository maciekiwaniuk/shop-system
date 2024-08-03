<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\QueryBus;

use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryInterface;

interface QueryBusInterface
{
    public function handle(QueryInterface $query): QueryResult;
}
