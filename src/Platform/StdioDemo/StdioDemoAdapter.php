<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\StdioDemo;

use Clue\React\Stdio\Stdio;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Platform\ShellAdapter;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioDemoAdapter implements ShellAdapter
{

    /**
     * @var StdioDemoPlatform
     */
    protected $platform;

    /**
     * @var Stdio
     */
    protected $stdio;

    /**
     * @var string
     */
    protected $line;

    /**
     * @var AppRequest|null
     */
    protected $request;

    /**
     * StdioDemoAdapter constructor.
     * @param Stdio $stdio
     * @param string $line
     */
    public function __construct(Stdio $stdio, string $line)
    {
        $this->stdio = $stdio;
        $this->line = $line;
    }

    public function getRequest(): AppRequest
    {
        // TODO: Implement getRequest() method.
    }

    public function sendResponse(AppResponse $response): void
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


}