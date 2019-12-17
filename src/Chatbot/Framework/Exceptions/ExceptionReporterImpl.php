<?php


namespace Commune\Chatbot\Framework\Exceptions;


use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Contracts\ExceptionReporter;

/**
 * 系统默认的异常通知工具.
 * 可以将之替换为 Sentry 等.
 *
 * 注意这个是进程级别服务!!!
 *
 * 在配置文件里注册.
 *
 * @see \Commune\Chatbot\Framework\Providers\ExceptionReporterServiceProvider
 * @see \Commune\Chatbot\Config\Children\BaseServicesConfig
 *
 */
class ExceptionReporterImpl implements ExceptionReporter
{
    /**
     * @var ConsoleLogger
     */
    protected $consoleLogger;

    /**
     * ExceptionReporterImpl constructor.
     * @param ConsoleLogger $consoleLogger
     */
    public function __construct(ConsoleLogger $consoleLogger)
    {
        $this->consoleLogger = $consoleLogger;
    }

    public function report(
        string $level,
        \Throwable $e,
        array $context = []
    ): void
    {
        $this->consoleLogger->log($level, strval($e), $context);
    }


}