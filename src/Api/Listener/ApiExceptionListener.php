<?php

namespace App\Api\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class ApiExceptionListener
 * @package App\Api\Listener
 */
class ApiExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /** @var \Exception $exception */
        $exception = $event->getException();
        $response = new JsonResponse($this->buildResponseData($exception));

        $exceptionCode = $event->getException()->getCode();

        if ($exceptionCode === 0) {
            $exceptionCode = Response::HTTP_NOT_FOUND;
        }

        $response->setStatusCode($exceptionCode);

        $event->setResponse($response);
    }

    /**
     * @param \Exception $exception
     * @return array
     */
    private function buildResponseData(\Exception $exception)
    {
        $messages = \json_decode($exception->getMessage());
        if (!is_array($messages)) {
            $messages = $exception->getMessage() ? [$exception->getMessage()] : [];
        }

        return [
            'error' => [
                'code' => $exception->getCode(),
                'messages' => $messages
            ]];
    }
}
