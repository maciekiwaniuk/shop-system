<?php

declare(strict_types=1);

namespace App\Common\Application\Bus\QueryBus;

use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

readonly class QueryBus implements QueryBusInterface
{
    public function __construct(
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(QueryInterface $query): QueryResult
    {
        $handledStamps = ($this->bus->dispatch($query))
            ->all(HandledStamp::class);

        $handledStamp = $handledStamps[0];
        $queryResult = $handledStamp->getResult();

        if (
            !$handledStamps
            || count($handledStamps) > 1
            || !isset($queryResult)
            || !$queryResult instanceof QueryResult
        ) {
            $this->logger->error('Query bus handler validation failed', [
                'query_class' => get_class($query),
                'handled_stamps_count' => count($handledStamps),
                'has_query_result' => isset($queryResult),
                'query_result_class' => isset($queryResult) ? get_class($queryResult) : null,
                'expected_result_class' => QueryResult::class,
            ]);
            return new QueryResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return $queryResult;
    }
}
