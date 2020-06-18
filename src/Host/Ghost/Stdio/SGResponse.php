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
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Protocals\HostMsg\DefaultIntents;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\IntercomMsg;

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
     * @var InputMsg
     */
    protected $input;

    /**
     * @var InputMsg[]
     */
    protected $asyncInputs;

    /**
     * @var IntercomMsg[]
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
        InputMsg $input,
        array $asyncInputs,
        array $outputs,
        int $code = 0,
        string $message = ''
    )
    {
        $this->stdio = $stdio;
        $this->console = $console;
        $this->input = $input;
        $this->asyncInputs = $asyncInputs;
        $this->outputs = $outputs;
        $this->errcode = $code;
        $this->errmsg = $message;
    }


    public function end() : ? callable
    {
        $quit = false;
        foreach ($this->outputs as $output) {
            $hostMsg = $output->getMessage();
            $level = $hostMsg->getLevel();
            $this->console->log($level, $hostMsg->getText() . "\n");

            if ($hostMsg->getProtocalId() === DefaultIntents::SYSTEM_SESSION_QUIT ) {
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

        return null;
    }

    public function getErrcode(): int
    {
        return $this->errcode;
    }

    public function getErrmsg(): string
    {
        return $this->errmsg;
    }

    public function getInput(): InputMsg
    {
        return $this->input;
    }

    public function getAsyncInputs(): array
    {
        return $this->asyncInputs;
    }

    public function getOutputs(): array
    {
        return $this->outputs;
    }


}