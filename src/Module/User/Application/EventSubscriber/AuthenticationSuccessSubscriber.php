<?php

declare(strict_types=1);

namespace App\Module\User\Application\EventSubscriber;

use App\Module\User\Domain\Entity\User;
use App\Module\User\Infrastructure\Doctrine\Repository\UserRepository;
use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    protected Request $request;

    public function __construct(
        protected readonly UserRepository $userRepository
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->request = $event->getRequest();
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        $user
            ->setLastLoginIp($this->request->getClientIp())
            ->setLastLoginUserAgent($this->request->headers->get('User-Agent'))
            ->setLastLoginTime(new DateTimeImmutable());

        $this->userRepository->save($user, true);

        $event->setData(
            [
                'success' => true,
                'data' => [
                    'token' => $event->getData()['token']
                ]
            ]
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }
}
