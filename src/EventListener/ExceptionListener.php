<?php

namespace App\EventListener;

use App\Exception\APIException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $e = $event->getThrowable();

        if ($e instanceof APIException) {
            $code = $e->getCode();
            $error = $e->getMessage();
        } elseif ($e instanceof NotFoundHttpException) {
            $code = 404;
            $error = 'Not found';
        } else {
            // @todo log to ...
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $error = 'Unknown error';
        }

        $customResponse = new JsonResponse([
            'success' => false,
            'status' => $code,
            'error' => $error,
        ], $code);
        $event->setResponse($customResponse);
    }
}