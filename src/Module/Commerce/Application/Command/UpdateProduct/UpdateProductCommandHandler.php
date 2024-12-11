<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\UpdateProduct;

use App\Common\Domain\Cache\CacheCreatorInterface;
use App\Common\Domain\Cache\CacheProxyInterface;
use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Commerce\Domain\Repository\ProductRepositoryInterface;
use App\Common\Application\BusResult\CommandResult;
use App\Common\Application\Command\CommandInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler(fromTransport: 'sync')]
readonly class UpdateProductCommandHandler implements CommandInterface
{
    protected CacheProxyInterface $cache;

    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger,
        CacheCreatorInterface $cacheCreator,
    ) {
        $this->cache = $cacheCreator->create('query.products.findProductBySlugQuery.');
    }

    public function __invoke(UpdateProductCommand $command): CommandResult
    {
        try {
            $this->cache->delByKeys([$command->product]);

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
