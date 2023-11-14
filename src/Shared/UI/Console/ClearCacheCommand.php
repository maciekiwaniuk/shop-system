<?php

declare(strict_types=1);

namespace App\Shared\UI\Console;

use App\Shared\Infrastructure\Cache\CacheCreator;
use App\Shared\Infrastructure\Cache\CacheProxy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:clear-cache',
    description: 'Clear cache'
)]
final class ClearCacheCommand extends Command
{
    protected readonly CacheProxy $cache;

    public function __construct(
        CacheCreator $cacheCreator
    ) {
        parent::__construct();
        $this->cache = $cacheCreator->create('');
    }

    protected function configure(): void
    {
        $this->setHelp('Clears cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->cache->delByKeys(
                $this->cache->keysByPrefix()
            );
            $output->writeln('Successfully cleared cache.');
            return Command::SUCCESS;
        } catch (Throwable $throwable) {
            $output->writeln(
                sprintf(
                    'There was a technical problem while clearing cache. Error: %s',
                    $throwable->getMessage()
                )
            );
        }
        return Command::FAILURE;
    }
}
