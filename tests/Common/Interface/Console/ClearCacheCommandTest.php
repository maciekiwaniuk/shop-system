<?php

declare(strict_types=1);

namespace App\Tests\Common\Interface\Console;

use App\Common\Infrastructure\Cache\CacheCreator;
use App\Common\Interface\Console\ClearCacheCommand;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ClearCacheCommandTest extends AbstractIntegrationTestCase
{
    public function testExecute(): void
    {
        $container = self::getContainer();

        /** @var CacheCreator $cacheCreator */
        $cacheCreator = $container->get(CacheCreator::class);
        $cache = $cacheCreator->create('examplePrefix.');
        $cache->set('key', 'value');

        $application = new Application();
        $application->add(
            new ClearCacheCommand(
                cacheCreator: $cacheCreator,
            ),
        );

        $command = $application->find('app:clear-cache');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully cleared cache.', $output);
        $this->assertEmpty($cache->keysByPrefix());
    }
}
