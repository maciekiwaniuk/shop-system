<?php

namespace App\Application\Bus\QueryBus;

use App\Application\Query\QueryResultInterface;

interface QueryBusInterface
{
    public function handle(object $message): QueryResultInterface;
}