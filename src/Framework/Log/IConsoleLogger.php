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

    /**
     * @var bool
     */
    protected $debug;

    /**
     * IConsoleLogger constructor.
     * @param bool $debug
     */
    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }


    public function log($level, $message, array $context = array())
    {
        if (!$this->debug && $level == LogLevel::DEBUG) {
            return;
        }

        $start = "[$level] ";
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
            case 'warning' :
                return "\033[33m$str\033[0m";
            default :
                return "\033[31m$str\033[0m";
        }
    }



}