<?php

/**
 * Class ExceptionHandler
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Commune\Chatbot\Blueprint\Exceptions\RuntimeExceptionInterface;
use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;

/**
 * 处理 conversation 中异常的 handler
 */
interface ExceptionHandler
{

    /**
     * @param string $method
     * @param StopServiceExceptionInterface $e
     */
    public function reportServiceStopException(
        string $method,
        StopServiceExceptionInterface $e
    ) : void;

    /**
     * @param string $method
     * @param RuntimeExceptionInterface $e
     */
    public function reportRuntimeException(
        string $method,
        RuntimeExceptionInterface $e
    ) : void;
}