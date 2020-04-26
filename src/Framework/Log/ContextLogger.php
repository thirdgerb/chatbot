<?php


namespace Commune\Framework\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Commune\Contracts\Log\ExceptionReporter;

class ContextLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var LoggerInterface
     */
    protected $monolog;

    /**
     * @var ExceptionReporter
     */
    protected $reporter;

    /**
     * @var array
     */
    protected $context;

    /**
     * MonologWriter constructor.
     * @param LoggerInterface $logger
     * @param ExceptionReporter $reporter
     * @param array $context
     */
    public function __construct(
        LoggerInterface $logger,
        ExceptionReporter $reporter,
        array $context = []
    )
    {
        $this->monolog = $logger;
        $this->reporter = $reporter;
        $this->context = $context;
    }

    public function log($level, $message, array $context = array())
    {
        // 尝试报告异常.
        if ($message instanceof \Throwable) {
            $this->reporter->report($message);
            $message = get_class($message) . ' : ' . $message->getMessage();
        }

        $this->monolog->log($level, $message, $context + $this->context);
    }


}