<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Query\SearchProductsByPhrase;

use App\Common\Application\Query\QueryInterface;

readonly class SearchProductsByPhraseQuery implements QueryInterface
{
    public function __construct(
        public string $phrase,
    ) {
    }
}
