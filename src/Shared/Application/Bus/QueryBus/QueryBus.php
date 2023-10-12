<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus\QueryBus;

use App\Shared\Application\BusResult\QueryResult;
use App\Shared\Application\Query\QueryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBus implements QueryBusInterface
{
    public function __construct(
        protected readonly MessageBusInterface $bus,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function handle(QueryInterface $query): QueryResult
    {
        $handledStamps = ($this->bus->dispatch($query))
            ->all(HandledStamp::class);

        $handledStamp = $handledStamps[0];
        if (method_exists($handledStamp, 'getResult')) {
            $queryResult = $handledStamp->getResult();
        }

        if (
            !$handledStamps
            || count($handledStamps) > 1
            || !isset($queryResult)
            || !$queryResult instanceof QueryResult
        ) {
            $this->logger->error('Something went wrong while handling action in query bus');
            return new QueryResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $queryResult;
    }
}
