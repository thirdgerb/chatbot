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
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Message\Host\Convo\IText;
use Commune\Message\Intercom\IGhostInput;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\GhostInput;
use Commune\Protocals\IntercomMsg;
use Commune\Support\DI\TInjectable;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SGRequest implements GhostRequest
{
    use TInjectable;

    /**
     * @var string
     */
    protected $line;

    /**
     * @var Stdio
     */
    protected $stdio;

    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * @var string
     */
    protected $shellName;

    /**
     * @var string
     */
    protected $shellId;

    /**
     * @var string
     */
    protected $senderId;

    /**
     * @var string
     */
    protected $sessionId;

    /*----- cached -----*/

    /**
     * @var GhostInput|null
     */
    protected $input;

    /**
     * SGRequest constructor.
     * @param string $line
     * @param Stdio $stdio
     * @param ConsoleLogger $console
     * @param string $shellName
     * @param string $shellId
     * @param string $sessionI
     * @param string $senderId
     */
    public function __construct(
        string $line,
        Stdio $stdio,
        ConsoleLogger $console,
        string $shellName = 'stdioTestName',
        string $shellId = 'stdioTestId',
        string $sessionI = 'stdioSessionId',
        string $senderId = 'stdioTestSender'
    )
    {
        $this->line = $line;
        $this->stdio = $stdio;
        $this->console = $console;
        $this->shellName = $shellName;
        $this->shellId = $shellId;
        $this->sessionId = $senderId;
        $this->senderId = $senderId;
    }

    /**
     * @return GhostInput
     */
    public function getInput(): IntercomMsg
    {
        return $this->input
            ?? $this->input = new IGhostInput(
                new IText($this->line),
                $this->shellId,
                $this->sessionId,
                $this->shellName,
                $this->shellId,
                $this->senderId
            );
    }

    public function output(HostMsg $message, HostMsg ...$messages): AppResponse
    {
        array_unshift($messages, $message);
        $input = $this->getInput();
        $outputs = array_map(function(HostMsg $msg) use ($input){
            return $input->output($msg);
        }, $messages);

        return new SGResponse(
            $this->stdio,
            $this->console,
            $outputs
        );
    }


    public function isValid(): bool
    {
        return true;
    }

    public function isStateless(): bool
    {
        return $this->getInput()->getMessage() instanceof HostMsg\Convo\ApiMsg;
    }

    public function noOutputs(): bool
    {
        return false;
    }

    /**
     * @param Cloner $output
     * @param int $errcode
     * @param string $errmsg
     * @return AppResponse
     */
    public function response(
        $output,
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ): AppResponse
    {
        return new SGResponse(
            $this->stdio,
            $this->console,
            $output->getOutputs(),
            $errcode,
            $errmsg
        );
    }

    public function fail(int $errcode, string $errmsg = ''): AppResponse
    {
        $errmsg = !empty($errmsg)
            ? $errmsg
            : AppResponse::DEFAULT_ERROR_MESSAGES[$errcode] ?? '';

        return new SGResponse(
            $this->stdio,
            $this->console,
            [],
            $errcode,
            $errmsg
        );
    }


}