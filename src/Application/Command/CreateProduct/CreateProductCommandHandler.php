<?php

namespace App\Application\Command\CreateProduct;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandHandlerInterface;
use App\Application\Command\CommandInterface;
use App\Domain\Entity\Product;
use App\Infrastructure\Doctrine\Repository\ProductRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class CreateProductCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(CommandInterface $command): CommandResult
    {
        try {
            $product = new Product(
                name: $command->dto->name,
                price: $command->dto->price
            );
            $this->productRepository->save($product);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new CommandResult(success: true, statusCode: Response::HTTP_CREATED);
    }
}