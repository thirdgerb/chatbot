<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\ExpReporter;

use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\ExceptionReporter;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ConsoleExceptionReporter implements ExceptionReporter
{
    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * ConsoleExceptionReporter constructor.
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