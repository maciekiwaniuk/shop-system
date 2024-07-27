<?php

declare(strict_types=1);

namespace App\Module\User\Application\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthenticationFailureSubscriber implements EventSubscriberInterface
{
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $response = $event->getResponse();

        $content = json_decode($response->getContent(), true);
        $content['success'] = false;

        $response->setContent(json_encode($content));
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }
}
