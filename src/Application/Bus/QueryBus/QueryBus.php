<?php

declare(strict_types=1);

namespace App\Application\Bus\QueryBus;

use App\Application\BusResult\CommandResult;
use App\Application\BusResult\QueryResult;
use App\Application\Query\QueryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBus extends MessageBus implements QueryBusInterface
{
    public function __construct(
        protected readonly LoggerInterface $logger,
        iterable $middlewareHandlers = []
    ) {
        parent::__construct($middlewareHandlers);
    }

    public function handle(QueryInterface $query): QueryResult
    {
        $envelope = $this->dispatch($query);
        $handledStamps = $envelope->all(HandledStamp::class);

        $result = $handledStamps[0]->getResult();
        if (!$handledStamps || count($handledStamps) > 1 || !$result instanceof QueryResult) {
            return new QueryResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $result;
    }
}