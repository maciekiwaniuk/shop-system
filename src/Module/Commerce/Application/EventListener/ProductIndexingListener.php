<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\EventListener;

use App\Module\Commerce\Domain\Event\ProductCreatedEvent;
use App\Module\Commerce\Domain\Event\ProductDeletedEvent;
use App\Module\Commerce\Domain\Event\ProductUpdatedEvent;
use App\Module\Commerce\Domain\Repository\ProductSearchRepositoryInterface;
use App\Module\Commerce\Infrastructure\Elasticsearch\ProductIndexManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

readonly class ProductIndexingListener
{
    public function __construct(
        private ProductSearchRepositoryInterface $productSearchRepository,
    ) {
    }

    #[AsEventListener(ProductCreatedEvent::class)]
    public function onProductCreated(ProductCreatedEvent $event): void
    {
        $this->productSearchRepository->indexProduct($event->product);
    }

    #[AsEventListener(ProductUpdatedEvent::class)]
    public function onProductUpdated(ProductUpdatedEvent $event): void
    {
        $this->productSearchRepository->indexProduct($event->product);
    }

    #[AsEventListener(ProductDeletedEvent::class)]
    public function onProductDeleted(ProductDeletedEvent $event): void
    {
        $this->productSearchRepository->removeProduct($event->id);
    }
}
