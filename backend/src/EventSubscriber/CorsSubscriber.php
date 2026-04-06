<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CorsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->getRequest()->getMethod() !== 'OPTIONS') {
            return;
        }

        $response = new Response('', Response::HTTP_OK);
        $this->addCorsHeaders($response);
        $event->setResponse($response);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $this->addCorsHeaders($event->getResponse());
    }

    private function addCorsHeaders(Response $response): void
    {
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept');
    }
}
