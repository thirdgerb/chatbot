<?php

/**
 * Class PipelineException
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;

use Throwable;

/**
 * 管道异常的记录点.
 */
class PipelineException extends RuntimeException
{

    public function __construct(string $pipeName, Throwable $previous = null)
    {
        parent::__construct(
            "pipeline $pipeName exception",
            $previous
        );
    }

}