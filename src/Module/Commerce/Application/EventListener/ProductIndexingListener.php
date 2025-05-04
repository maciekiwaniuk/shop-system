<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\EventListener;

use App\Module\Commerce\Domain\Event\ProductCreatedEvent;
use App\Module\Commerce\Domain\Event\ProductDeletedEvent;
use App\Module\Commerce\Domain\Event\ProductUpdatedEvent;
use App\Module\Commerce\Infrastructure\Elasticsearch\Product\ProductIndexManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

readonly class ProductIndexingListener
{
    public function __construct(
        private ProductIndexManager $productIndexManager,
    ) {
    }

    #[AsEventListener(ProductCreatedEvent::class)]
    public function onProductCreated(ProductCreatedEvent $event): void
    {
        $this->productIndexManager->indexProduct($event->dto);
    }

    #[AsEventListener(ProductUpdatedEvent::class)]
    public function onProductUpdated(ProductUpdatedEvent $event): void
    {
        $this->productIndexManager->indexProduct($event->dto);
    }

    #[AsEventListener(ProductDeletedEvent::class)]
    public function onProductDeleted(ProductDeletedEvent $event): void
    {
        $this->productIndexManager->removeProduct($event->id);
    }
}
