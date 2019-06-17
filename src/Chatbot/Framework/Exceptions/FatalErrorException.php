<?php

/**
 * Class FatalErrorException
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;


use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;

class FatalErrorException extends \RuntimeException implements StopServiceExceptionInterface
{


    public function __construct(\Throwable $e)
    {
        parent::__construct(
            'fatal error occur during running ',
            255,
            $e
        );
    }
}