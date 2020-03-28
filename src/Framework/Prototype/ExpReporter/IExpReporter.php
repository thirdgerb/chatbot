<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\ExpReporter;

use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\ExceptionReporter;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IExpReporter implements ExceptionReporter
{
    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * IExpReporter constructor.
     * @param ConsoleLogger $console
     */
    public function __construct(ConsoleLogger $console)
    {
        $this->console = $console;
    }


    public function report(\Throwable $e): void
    {
        $this->console->critical(strval($e));
    }
}