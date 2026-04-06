<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire('%kernel.debug%')]
        private readonly bool $debug,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $response = [
            'code' => 'internal_error',
            'message' => 'Vyskytla se neočekávaná chyba',
        ];

        if ($this->debug) {
            $response['details'] = $event->getThrowable()->getMessage();
            $response['trace'] = $event->getThrowable()->getTrace();
        }

        $event->setResponse(new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
