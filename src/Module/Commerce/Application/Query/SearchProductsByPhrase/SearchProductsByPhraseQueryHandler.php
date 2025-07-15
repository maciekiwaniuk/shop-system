<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\SearchProductsByPhrase;

use App\Common\Application\BusResult\QueryResult;
use App\Common\Application\Query\QueryHandlerInterface;
use App\Module\Commerce\Domain\Repository\ProductSearchRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class SearchProductsByPhraseQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private ProductSearchRepositoryInterface $productSearchRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SearchProductsByPhraseQuery $query): QueryResult
    {
        try {
            $products = $this->productSearchRepository->searchByPhrase($query->phrase);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to search products by phrase', [
                'search_phrase' => $query->phrase,
                'error' => $exception->getMessage(),
                'exception_class' => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
            return new QueryResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new QueryResult(
            success: true,
            statusCode: Response::HTTP_OK,
            data: $products,
        );
    }
}
