<?php

declare(strict_types=1);

namespace App\Tests\Module\User\UI;

use App\Module\User\UI\Console\CreateUserCommand;
use App\Shared\Application\Bus\CommandBus\CommandBusInterface;
use App\Tests\AbstractApplicationTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandTest extends AbstractApplicationTestCase
{
    public function testExecute(): void
    {
        $container = static::getContainer();

        /** @var CommandBusInterface $commandBus */
        $commandBus = $container->get(CommandBusInterface::class);

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        $application = new Application();
        $application->add(
            new CreateUserCommand(
                commandBus: $commandBus,
                validator: $validator
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
