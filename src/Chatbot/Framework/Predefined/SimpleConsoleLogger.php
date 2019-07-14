<?php

/**
 * Class ConsoleLogImpl
 * @package Commune\Chatbot\Framework\Predefined
 */

namespace Commune\Chatbot\Framework\Predefined;


use Commune\Chatbot\Contracts\ConsoleLogger;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class SimpleConsoleLogger implements ConsoleLogger
{
    use LoggerTrait;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * SimpleConsoleLogger constructor.
     */
    public function __construct()
    {
        $this->debug = CHATBOT_DEBUG;
    }


    public function log($level, $message, array $context = array())
    {
        if (!$this->debug && $level == LogLevel::DEBUG) {
            return;
        }

        $start = "[$level] ";
        // 打印日志message
        print_r( $start . $this->wrapMessage($level, $message) . PHP_EOL);

        // 打印日志context
        if(!empty($context)) {
            $contextJson = json_encode(
                $context,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );

            $str = $this->wrapMessage($level, $contextJson);
            print_r($start . $str . PHP_EOL);
        }
    }

    public static function wrapMessage(string $level, string $str) : string
    {
        switch($level) {
            case 'debug' :
                return $str;
            case 'info' :
                return "\033[32m$str\033[0m";
            case 'warning' :
                return "\033[33m$str\033[0m";
            default :
                return "\033[31m$str\033[0m";
        }
    }


}