<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Providers;

use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\ExpReporter\ConsoleExceptionReporter;

/**
 * 简单的异常报告模块实现.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SplExpReporterServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            ExceptionReporter::class,
            ConsoleExceptionReporter::class
        );
    }


}