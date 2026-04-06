<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

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
        $throwable = $event->getThrowable();

        if ($throwable instanceof HttpException && $throwable->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            $cause = $throwable->getPrevious();

            if ($cause instanceof ValidationFailedException) {
                $fields = [];
                foreach ($cause->getViolations() as $violation) {
                    $fields[$violation->getPropertyPath()][] = $violation->getMessage();
                }

                $event->setResponse(new JsonResponse([
                    'code' => 'validation_failed',
                    'message' => 'Validace selhala.',
                    'fields' => $fields,
                ], Response::HTTP_BAD_REQUEST));

                return;
            }

            $event->setResponse(new JsonResponse([
                'code' => 'invalid_request',
                'message' => 'Požadavek nelze zpracovat.',
            ], Response::HTTP_BAD_REQUEST));

            return;
        }

        $response = [
            'code' => 'internal_error',
            'message' => 'Vyskytla se neočekávaná chyba',
        ];

        if ($this->debug) {
            $response['details'] = $throwable->getMessage();
            $response['trace'] = $throwable->getTrace();
        }

        $event->setResponse(new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
