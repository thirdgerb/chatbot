<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Providers;

use Commune\Framework\Prototype\Log\MonologWriter;
use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ExceptionReporter;
use Commune\Framework\Contracts\ServiceProvider;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Monolog\Logger as Monolog;

/**
 * Monolog 日志组件.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name          日志的标记, 通常是 shellName 或 chatbotName
 * @property-read string $path          日志文件存储的目录
 * @property-read int $days             为0 表示不轮换, 否则会按日增加文件.
 * @property-read string $level         日志级别.
 * @property-read bool $bubble          是否冒泡到别的handler
 * @property-read bool|null $permission 文件的权限设置
 * @property-read bool $locking         是否文件锁
 */
class MonologServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'name' => 'commune',
            'path' => __DIR__,
            'days' => 7,
            'level' => LogLevel::DEBUG,
            'bubble' => true,
            'permission' => null,
            'locking' => false,
        ];
    }

    public function isProcessServiceProvider(): bool
    {
        return true;
    }

    public function boot(ContainerContract $app): void
    {
    }

    /**
     * @param ContainerContract $app
     * @throws \Exception
     */
    public function register(ContainerContract $app): void
    {
        $app->instance(
            LoggerInterface::class,
            $this->makeLogger($app)
        );
    }

    /**
     * @param ContainerContract $app
     * @return LoggerInterface
     * @throws \Exception
     */
    protected function makeLogger(ContainerContract $app) : LoggerInterface
    {
        $config = $this;

        $level = Monolog::toMonologLevel($config->level);

        if ($config->days > 0) {
            $handler = new RotatingFileHandler(
                $config->path,
                $config->days,
                $level,
                $config->bubble,
                $config->permission,
                $config->locking
            );
        } else {
            $handler = new StreamHandler(
                $config->path,
                $level,
                $config->bubble,
                $config->permission,
                $config->locking
            );
        }

        $logger = new Monolog(
            $this->name,
            [$handler]
        );

        /**
         * @var ExceptionReporter $reporter
         */
        $reporter = $app->make(ExceptionReporter::class);
        return new MonologWriter($logger, $reporter);
    }


}