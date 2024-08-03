<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\UpdateProduct;

use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Commerce\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\Command\CommandInterface;
use App\Common\Infrastructure\Cache\CacheCreator;
use App\Common\Infrastructure\Cache\CacheProxy;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
readonly class UpdateProductCommandHandler implements CommandInterface
{
    protected CacheProxy $cache;

    public function __construct(
        protected ProductRepository $productRepository,
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger,
        CacheCreator $cacheCreator,
    ) {
        $this->cache = $cacheCreator->create('query.products.findProductBySlugQuery.');
    }

    public function __invoke(UpdateProductCommand $command): CommandResult
    {
        try {
            $this->cache->delByKeys([$command->product->getSlug()]);

            $user = $this->entityManager->getReference(Product::class, $command->product->getId());
            $user
                ->setName($command->dto->name)
                ->setPrice($command->dto->price);
            $this->entityManager->flush();
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_OK);
    }
}
