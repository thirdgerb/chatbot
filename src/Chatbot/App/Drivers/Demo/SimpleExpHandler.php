<?php


namespace Commune\Chatbot\App\Drivers\Demo;


use Commune\Chatbot\Blueprint\Exceptions\RuntimeExceptionInterface;
use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Psr\Log\LoggerInterface;

class SimpleExpHandler implements ExceptionHandler
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SimpleExceptionHandler constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function reportServiceStopException(
        string $method,
        StopServiceExceptionInterface $e
    ): void
    {
        $this->logger->critical(
            __METHOD__
            . ' '. $method
            . ' fatal exception : ' . $e
        );
    }

    public function reportRuntimeException(
        string $method,
        RuntimeExceptionInterface $e
    ): void
    {
        $this->logger->critical(
            __METHOD__
            . ' '. $method
            . ' runtime exception : ' . $e
        );
    }


}