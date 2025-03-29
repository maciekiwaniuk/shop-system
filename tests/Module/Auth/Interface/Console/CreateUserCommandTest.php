<?php

declare(strict_types=1);

namespace App\Tests\Module\Auth\Interface\Console;

use App\Module\Auth\Domain\Enum\UserRole;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Interface\Console\CreateUserCommand;
use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Tests\AbstractIntegrationTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandTest extends AbstractIntegrationTestCase
{
    private UserRepositoryInterface $userRepository;
    private Application $application;

    protected function setUp(): void
    {
        parent::setUp();
        $container = self::getContainer();

        /** @var SyncCommandBusInterface $syncCommandBus */
        $syncCommandBus = $container->get(SyncCommandBusInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = $container->get(QueryBusInterface::class);

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        $this->userRepository = $container->get(UserRepositoryInterface::class);

        $this->application = new Application();
        $this->application->add(
            new CreateUserCommand(
                syncCommandBus: $syncCommandBus,
                queryBus: $queryBus,
                validator: $validator,
                entityManager: $this->entityManager,
            ),
        );
    }

    /** @test */
    public function it_should_create_user_successfully(): void
    {
        $command = $this->application->find('app:create-user');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'email' => 'example@mail.pl',
            'password' => 'examplePassword',
            'name' => 'Maciek',
            'surname' => 'Iwaniuk',
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully created user.', $output);
        $this->assertNotEmpty($this->userRepository->findUserByEmail('example@mail.pl'));
    }

    /** @test */
    public function it_should_create_user_and_set_as_admin_when_flag_is_provided(): void
    {
        $command = $this->application->find('app:create-user');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'email' => 'example@mail.pl',
            'password' => 'examplePassword',
            'name' => 'Maciek',
            'surname' => 'Iwaniuk',
            'isAdmin' => '1',
        ]);

        $user = $this->userRepository->findUserByEmail('example@mail.pl');

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully created user.', $output);
        $this->assertStringContainsString('Successfully set user as admin.', $output);
        $this->assertNotEmpty($user);
        $this->assertContains(UserRole::ADMIN->value, $user->getRoles());
    }
}
