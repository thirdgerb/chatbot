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
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Protocals\HostMsg\IntentMsg;
use Commune\Protocals\Intercom\GhostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SGResponse implements GhostResponse
{
    /**
     * @var Stdio
     */
    protected $stdio;

    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * @var GhostMsg[]
     */
    protected $outputs = [];

    /**
     * @var int
     */
    protected $errcode;

    /**
     * @var string
     */
    protected $errmsg;

    public function __construct(
        Stdio $stdio,
        ConsoleLogger $console,
        array $outputs,
        int $code = 0,
        string $message = ''
    )
    {
        $this->stdio = $stdio;
        $this->console = $console;
        $this->outputs = $outputs;
        $this->outputs = $outputs;
        $this->errcode = $code;
        $this->errmsg = $message;
    }


    public function send(): void
    {
        $quit = false;
        foreach ($this->outputs as $output) {
            $hostMsg = $output->getMessage();
            $level = $hostMsg->getLevel();
            $this->console->log($level, $hostMsg->getText());

            if ($hostMsg->getText() === IntentMsg::SYSTEM_SESSION_QUIT ) {
                $quit = true;
            }
        }

        if ($quit) {
            $this->stdio->end('quit');
        }

        if ($this->errcode > 399) {
            $this->console->emergency($this->errmsg);
            $this->stdio->end($this->errcode);
        }
    }

    public function getCode(): int
    {
        return $this->errcode;
    }

    public function getMessage(): string
    {
        return $this->errmsg;
    }


}