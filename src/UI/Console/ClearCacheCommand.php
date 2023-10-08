<?php

declare(strict_types=1);

namespace App\UI\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:clear-cache',
    description: 'Clear cache'
)]
final class ClearCacheCommand extends Command
{
    public function __construct(
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Clears cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
