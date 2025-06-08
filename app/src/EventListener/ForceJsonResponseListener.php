<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class ForceJsonResponseListener
{
    public function onRequestEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Force content type to JSON
        $request->setRequestFormat('json');
    }
}
