<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    /** @phpstan-ignore-next-line */
    private Request $request;

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->request = $event->getRequest();
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $event->setData(
            [
                'success' => true,
                'data' => [
                    'token' => $event->getData()['token'],
                ],
            ],
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
