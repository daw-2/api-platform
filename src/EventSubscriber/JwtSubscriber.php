<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JwtSubscriber implements EventSubscriberInterface
{
    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        $event->setData(array_merge($event->getData(), [
            'email' => $event->getUser()->getUserIdentifier(),
        ]));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJwtCreated'
        ];
    }
}
