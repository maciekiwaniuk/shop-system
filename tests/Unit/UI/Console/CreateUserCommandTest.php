<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Console;

use App\Tests\Unit\AbstractUnitTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends AbstractUnitTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application();

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'test1234@wp.pl',
            'password' => 'test1234',
            'name' => 'Maciek',
            'surname' => 'Iwaniuk'
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully created user.', $output);
    }
}
