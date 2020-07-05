<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Log;

use Commune\Contracts\Log\ConsoleLogger;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IConsoleLogger implements ConsoleLogger
{
    use LoggerTrait;

    const LEVEL_ORDER = [
        LogLevel::EMERGENCY => 7,
        LogLevel::ALERT     => 6,
        LogLevel::CRITICAL  => 5,
        LogLevel::ERROR     => 4,
        LogLevel::WARNING   => 3,
        LogLevel::NOTICE    => 2,
        LogLevel::INFO      => 1,
        LogLevel::DEBUG     => 0,
    ];

    /**
     * @var bool
     */
    protected $showLevel;

    /**
     * @var int
     */
    protected $startLevel;

    /**
     * IConsoleLogger constructor.
     * @param bool $showLevel
     * @param string $startLevel
     */
    public function __construct(
        bool $showLevel = true,
        string $startLevel = LogLevel::DEBUG
    )
    {
        $this->showLevel = $showLevel;
        $this->startLevel = self::LEVEL_ORDER[$startLevel] ?? 0;
    }


    public function log($level, $message, array $context = array())
    {
        $order = self::LEVEL_ORDER[$level] ?? 0;
        if ($order < $this->startLevel) {
            return;
        }

        $start = $this->showLevel
            ? '[' . static::wrapMessage($level, strtoupper($level)) . '] '
            : '';
        // 打印日志message
        $this->write( $start . $this->wrapMessage($level, $message) . PHP_EOL);

        // 打印日志context
        if(!empty($context)) {
            $contextJson = json_encode(
                $context,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );

            $str = $this->wrapMessage($level, $contextJson);
            $this->write($start . $str . PHP_EOL);
        }
    }

    protected function write(string $string) : void
    {
        print_r($string);
    }

    public static function wrapMessage(string $level, string $str) : string
    {
        switch($level) {
            case 'debug' :
                return "\033[37m$str\033[0m";
            case 'info' :
                return "\033[32m$str\033[0m";
            case 'notice':
            case 'warning' :
                return "\033[33m$str\033[0m";
            default :
                return "\033[31m$str\033[0m";
        }
    }



}