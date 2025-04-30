<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\SendWelcomeEmail;

use App\Common\Application\AsyncCommand\AsyncCommandHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Twig\Environment;

#[AsMessageHandler]
readonly class SendWelcomeEmailCommandHandler implements AsyncCommandHandlerInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
    ) {
    }

    public function __invoke(SendWelcomeEmailCommand $command): void
    {
        $htmlBody = $this->twig->render('emails/welcome.html.twig', [
            'fullName' => $command->dto->name . ' ' . $command->dto->surname,
        ]);

        $email = new Email()
            ->from('no-reply@example.com')
            ->to($command->dto->email)
            ->subject('Welcome to our shop!')
            ->html($htmlBody);

        $this->mailer->send($email);
    }
}