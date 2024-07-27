<?php

declare(strict_types=1);

namespace App\Module\Product\Application\Command\CreateProduct;

use App\Module\Product\Domain\Entity\Product;
use App\Module\Product\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Shared\Application\BusResult\CommandResult;
use App\Shared\Application\Command\CommandHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class CreateProductCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateProductCommand $command): CommandResult
    {
        try {
            $product = new Product(
                name: $command->dto->name,
                price: $command->dto->price,
            );
            $this->productRepository->save($product, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}
