<?php

declare(strict_types=1);

namespace App\Module\Commerce\Interface\Console;

use App\Module\Commerce\Infrastructure\Elasticsearch\ElasticsearchIndexException;
use App\Module\Commerce\Infrastructure\Elasticsearch\Product\ProductIndexManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'commerce:elasticsearch:create-product-index')]
final class CreateProductIndexCommand extends Command
{
    public function __construct(
        private readonly ProductIndexManager $productIndexManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->productIndexManager->createIndex();
            $output->writeln('Product index created successfully.');
            return Command::SUCCESS;
        } catch (ElasticsearchIndexException $exception) {
            $output->writeln('Failed to create product index: ' . $exception->getMessage());
            return Command::FAILURE;
        }
    }
}
