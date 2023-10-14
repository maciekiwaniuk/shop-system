<?php

declare(strict_types=1);

namespace App\Tests\Module\User\Application\UI;

use App\Shared\Application\Bus\CommandBus\CommandBusInterface;
use App\Module\User\UI\Console\CreateUserCommand;
use App\Tests\AbstractApplicationTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandTest extends AbstractApplicationTestCase
{
    public function testExecute(): void
    {
        $container = static::getContainer();

        $application = new Application();
        $application->add(
            new CreateUserCommand(
                commandBus: $container->get(CommandBusInterface::class),
                validator: $container->get(ValidatorInterface::class)
            )
        );

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
