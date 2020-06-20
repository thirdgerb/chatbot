<?php


namespace Commune\Framework\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

abstract class ContextLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var array|null
     */
    protected $context;

    abstract protected function getLogger() : LoggerInterface;

    abstract protected function report(\Throwable $e) : void;

    abstract protected function makeContext() : array;

    protected function getContext(): array
    {
        if (isset($this->context)) {
            return $this->context;
        }

        return $this->context = $this->makeContext();
    }

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