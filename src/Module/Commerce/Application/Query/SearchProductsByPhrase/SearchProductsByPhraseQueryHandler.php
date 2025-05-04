<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\SearchProductsByPhrase;

use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use App\Common\Domain\Serializer\JsonSerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class SearchProductsByPhraseQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private JsonSerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SearchProductsByPhraseQuery $query): QueryResult
    {
        try {
//            $products = ;
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new QueryResult(
                success: false,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
//            data: json_decode($this->serializer->serialize($products), true),
        );
    }
}
