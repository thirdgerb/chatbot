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
            $this->report($message);
            $error = get_class($message) . ' : ' . $message->getMessage();
            $exp = $message;

            while($prev = $exp->getPrevious()) {
                $error .= ', prev ' . $prev->getMessage();
                $exp = $prev;
            }


        } else {
            $error = strval($message);
        }

        $this->getLogger()->log($level, $error, $context + $this->getContext());
    }


}