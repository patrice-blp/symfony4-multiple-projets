<?php

namespace App\Api\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class BadRequestException
 * @package App\Api\Exception
 */
class NotFoundException extends \Exception implements ApiExceptionInterface
{
    /**
     * BadRequestException constructor.
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND);
    }
}
