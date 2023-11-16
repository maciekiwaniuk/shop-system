<?php

declare(strict_types=1);

namespace App\Tests\Module\User\UI\Console;

use App\Module\User\Domain\Repository\UserRepositoryInterface;
use App\Module\User\UI\Console\CreateUserCommand;
use App\Shared\Application\Bus\CommandBus\CommandBusInterface;
use App\Shared\Application\Bus\QueryBus\QueryBusInterface;
use App\Shared\Infrastructure\Serializer\JsonSerializer;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandTest extends AbstractIntegrationTestCase
{
    protected UserRepositoryInterface $userRepository;
    protected Application $application;

    protected function setUp(): void
    {
        parent::setUp();
        $container = self::getContainer();

        /** @var CommandBusInterface $commandBus */
        $commandBus = $container->get(CommandBusInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = $container->get(QueryBusInterface::class);

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        /** @var JsonSerializer $serializer */
        $serializer = $container->get(JsonSerializer::class);

        $this->userRepository = $container->get(UserRepositoryInterface::class);

        $this->application = new Application();
        $this->application->add(
            new CreateUserCommand(
                commandBus: $commandBus,
                queryBus: $queryBus,
                validator: $validator,
                serializer: $serializer
            )
        );
    }

    public function testExecute(): void
    {
        $command = $this->application->find('app:create-user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'example@mail.pl',
            'password' => 'examplePassword',
            'name' => 'Maciek',
            'surname' => 'Iwaniuk'
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully created user.', $output);
        $this->assertNotEmpty($this->userRepository->findUserByEmail('example@mail.pl'));
    }

    public function testExecuteWithIsAdmin(): void
    {
        $command = $this->application->find('app:create-user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'example@mail.pl',
            'password' => 'examplePassword',
            'name' => 'Maciek',
            'surname' => 'Iwaniuk',
            'isAdmin' => '1'
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully created user.', $output);
        $this->assertStringContainsString('Successfully set user as admin.', $output);
        $this->assertNotEmpty($this->userRepository->findUserByEmail('example@@mail.pl'));
    }
}
