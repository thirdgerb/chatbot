<?php

namespace Commune\Chatbot\Contracts;

use Commune\Chatbot\App\ChatPipe\UserMessengerPipe;
use Commune\Chatbot\Blueprint\Exceptions\ExceptionReporterImpl;

/**
 * 报告异常.
 *
 * @see UserMessengerPipe
 * @see ExceptionReporterImpl
 */
interface ExceptionReporter
{
    /**
     * 记录异常. 比如发送给 sentry
     * 整合在日志模块中使用.
     *
     * @see \Commune\Chatbot\Framework\Impl\MonologWriter
     *
     * @param string $level
     * @param \Throwable $e
     * @param array $context
     */
    public function report(
        string $level,
        \Throwable $e,
        array $context = []
    ) : void;

}