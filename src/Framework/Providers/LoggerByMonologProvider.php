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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Exceptions\CommuneBootingException;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\Log\MonologWriter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Monolog\Logger as Monolog;

/**
 * 基于 Monolog 实现的日志模块
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name 日志的名称
 * @property-read string $logDir 日志文件所在的目录.
 * @property-read string $file   日志文件的相对路径.
 * @property-read int $days  为0 表示不轮换, 否则会按日换文件.
 * @property-read string $level 日志级别.
 * @property-read bool $bubble 是否冒泡到别的handler
 * @property-read bool|null $permission 文件的权限设置
 * @property-read bool $locking 是否文件锁
 */
class LoggerByMonologProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'name' => 'commune',
            'logDir' => CommuneEnv::getLogPath(),
            'file' => 'commune.log',
            'days' => 7,
            'level' => LogLevel::DEBUG,
            'bubble' => true,
            'permission' => null,
            'locking' => false,
        ];
    }

    public function getId(): string
    {
        return LoggerInterface::class;
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }


    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            LoggerInterface::class,
            function($app) {
                return $this->makeLogger($app);
            }
        );
    }


    /**
     * @param ContainerContract $app
     * @return LoggerInterface
     */
    public function makeLogger(ContainerContract $app) : LoggerInterface
    {
        $level = Monolog::toMonologLevel($this->level);

        $path = rtrim($this->logDir, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . ltrim($this->file, DIRECTORY_SEPARATOR);

        if ($this->days > 0) {
            $handler = new RotatingFileHandler(
                $path,
                $this->days,
                $level,
                $this->bubble,
                $this->permission,
                $this->locking
            );

        } else {
            try {

                $handler = new StreamHandler(
                    $path,
                    $level,
                    $this->bubble,
                    $this->permission,
                    $this->locking
                );

            } catch (\Exception $e) {
                throw new CommuneBootingException(
                    "initialize Logger StreamHandler fail",
                    $e
                );
            }
        }

        $logger = new Monolog(
            $this->name,
            [$handler]
        );

        /**
         * @var ExceptionReporter $reporter
         */
        $reporter = $app->get(ExceptionReporter::class);
        return new MonologWriter($logger, $reporter);
    }



}