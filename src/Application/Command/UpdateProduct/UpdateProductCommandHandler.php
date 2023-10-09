<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateProduct;

use App\Application\BusResult\CommandResult;
use App\Application\Command\CommandInterface;
use App\Infrastructure\Doctrine\Repository\ProductRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class UpdateProductCommandHandler implements CommandInterface
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(UpdateProductCommand $command): CommandResult
    {
        try {
            $command->product
                ->setName($command->dto->name)
                ->setPrice($command->dto->price);
            $this->productRepository->save($command->product, true);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return new CommandResult(success: false, statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new CommandResult(success: true, statusCode: Response::HTTP_OK);
    }
}
