<?php

declare(strict_types=1);

namespace App\UI\Console;

use App\Application\Bus\CommandBus\CommandBusInterface;
use App\Application\DTO\User\CreateUserDTO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Application\Command\CreateUser\CreateUserCommand as CreateUserCommandEvent;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create user'
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly ValidatorInterface $validator
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
            ->addArgument('surname', InputArgument::REQUIRED, 'Surname');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $createUserDTO = new CreateUserDTO(
            email: $input->getArgument('email'),
            password: $input->getArgument('password'),
            name: $input->getArgument('name'),
            surname: $input->getArgument('surname')
        );
        var_dump('1');

        $errors = $this->validator->validate($createUserDTO);
        if (count($errors) > 0) {
            $output->writeln(
                'There was a problem while creating user. Errors: ' . $this->validationErrorsToString($errors)
            );
            var_dump($this->validationErrorsToString($errors));
            var_dump('test');
            return Command::FAILURE;
        }
        var_dump('2');

        $commandResult = $this->commandBus->handle(new CreateUserCommandEvent($createUserDTO));
        if ($commandResult->success) {
            $output->writeln('Successfully created user.');
            return Command::SUCCESS;
        }
        var_dump('3');

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
