<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Ghost\Stdio;

use Clue\React\Stdio\Stdio;
use Commune\Framework\Log\IConsoleLogger;
use Psr\Log\LogLevel;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SGConsoleLogger extends IConsoleLogger
{
    /**
     * @var Stdio
     */
    protected $stdio;


    public function __construct(
        Stdio $stdio,
        bool $showLevel = true,
        string $startLevel = LogLevel::DEBUG
    )
    {
        $this->stdio = $stdio;
        parent::__construct($showLevel, $startLevel);
    }

    protected function write(string $string): void
    {
        $this->stdio->write($string);
    }
}