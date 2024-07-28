<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Console;

use App\Module\Auth\Application\Command\CreateUser\CreateUserCommand as CreateUserCommandEvent;
use App\Module\Auth\Application\Command\SetUserAsAdmin\SetUserAsAdminCommand;
use App\Module\Auth\Application\DTO\CreateUserDTO;
use App\Module\Auth\Application\Query\FindUserByEmail\FindUserByEmailQuery;
use App\Module\Auth\Domain\Entity\User;
use App\Shared\Application\Bus\CommandBus\CommandBusInterface;
use App\Shared\Application\Bus\QueryBus\QueryBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create user',
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBusInterface $queryBus,
        protected readonly ValidatorInterface $validator,
        protected readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Creates user')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('name', InputArgument::REQUIRED, 'Name')
            ->addArgument('surname', InputArgument::REQUIRED, 'Surname')
            ->addArgument('isAdmin', InputArgument::OPTIONAL, 'Is admin | 1 to set it');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dto = new CreateUserDTO(
            email: $input->getArgument('email'),
            password: $input->getArgument('password'),
            name: $input->getArgument('name'),
            surname: $input->getArgument('surname'),
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $output->writeln(
                'There was a problem while creating user. Errors: ' . $this->validationErrorsToString($errors),
            );
            return Command::FAILURE;
        }

        $commandResult = $this->commandBus->handle(new CreateUserCommandEvent($dto));
        if ($commandResult->success) {
            $output->writeln('Successfully created user.');

            $queryResult = $this->queryBus->handle(new FindUserByEmailQuery($dto->email));
            if (array_key_exists('id', $queryResult->data)) {
                $user = $this->entityManager->getReference(User::class, $queryResult->data['id']);
                $commandResult = $this->commandBus->handle(new SetUserAsAdminCommand($user));

                if ($commandResult->success) {
                    $output->writeln('Successfully set user as admin.');
                }
            }
            return Command::SUCCESS;
        }

        $output->writeln('There was a technical problem while creating user.');
        return Command::FAILURE;
    }

    protected function validationErrorsToString(ConstraintViolationListInterface $errors): string
    {
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[$error->getPropertyPath()] = $error->getMessage();
        }
        return implode(' | ', $errorsArray);
    }
}
