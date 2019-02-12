<?php

/**
 * Class ExceptionHandlerImpl
 * @package Commune\Chatbot\Laravel\Impl
 */

namespace Commune\Chatbot\Demo\Impl;


use Commune\Chatbot\Contracts\ExceptionHandler;
use Psr\Log\LoggerInterface;

class ExceptionHandlerDemo implements ExceptionHandler
{
    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * ExceptionHandlerImpl constructor.
     * @param LoggerInterface $log
     */
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }


    public function handle(\Exception $e)
    {
        $this->log->error(get_class($e) . ':' . $e->getMessage());
    }


}