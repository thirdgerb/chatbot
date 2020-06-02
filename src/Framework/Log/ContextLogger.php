<?php


namespace Commune\Framework\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Commune\Contracts\Log\ExceptionReporter;

abstract class ContextLogger implements LoggerInterface
{
    use LoggerTrait;

    abstract protected function getLogger() : LoggerInterface;

    abstract protected function getReporter() : ExceptionReporter;

    abstract protected function getContext() : array;

    public function log($level, $message, array $context = array())
    {
        // 尝试报告异常.
        if ($message instanceof \Throwable) {
            $this->getReporter()->report($message);
            $message = get_class($message) . ' : ' . $message->getMessage();
        }

        $this->getLogger()->log($level, $message, $context + $this->getContext());
    }


}