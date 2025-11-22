<?php

declare(strict_types=1);

namespace App\Common\Interface\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

#[AsCommand(
    name: 'app:messenger:failed-messages',
    description: 'Show failed messages count and information',
)]
final class ShowFailedMessagesCommand extends Command
{
    public function __construct(
        private readonly TransportInterface $failedTransport,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Shows information about failed messages in the dead letter queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->failedTransport instanceof MessageCountAwareInterface) {
            $count = $this->failedTransport->getMessageCount();
            $io->info("Failed messages count: $count");

            if ($count > 0) {
                $io->warning('There are failed messages in the queue!');
                $io->text('To retry all failed messages, run:');
                $io->text('  php bin/console messenger:failed:retry --force');
                $io->newLine();
                $io->text('To see details of failed messages, run:');
                $io->text('  php bin/console messenger:failed:show');
            } else {
                $io->success('No failed messages in the queue.');
            }
        } else {
            $io->warning('The failed transport does not support message counting.');
        }

        return Command::SUCCESS;
    }
}

