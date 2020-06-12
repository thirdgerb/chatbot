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
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\DI\TInjectable;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SGRequest implements GhostRequest
{
    use TInjectable;

    /**
     * @var HostMsg
     */
    protected $message;

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
    protected $guestId;

    /**
     * @var string
     */
    protected $guestName;

    /**
     * @var string
     */
    protected $sessionId;

    /*----- cached -----*/

    /**
     * @var InputMsg|null
     */
    protected $input;

    public function __construct(
        HostMsg $message,
        Stdio $stdio,
        string $shellName = 'stdioTestName',
        string $sessionId = 'stdioSessionId',
        string $guestId = 'stdioTestGuestId',
        string $guestName = 'stdioTestGuestName'
    )
    {
        $this->message = $message;
        $this->stdio = $stdio;
        $this->console = new SGConsoleLogger($stdio, false);
        $this->shellName = $shellName;
        $this->sessionId = $sessionId;
        $this->guestId = $guestId;
        $this->guestName = $guestName;
    }

    /**
     * @return InputMsg
     */
    public function getInput(): InputMsg
    {
        return $this->input
            ?? $this->input = new IInputMsg(
                $this->shellName,
                $this->message,
                $this->guestId,
                null,
                null,
                $this->sessionId,
                $this->guestName
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
            $input,
            [],
            $outputs,
            AppResponse::SUCCESS,
            AppResponse::DEFAULT_ERROR_MESSAGES[AppResponse::SUCCESS]
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


    /**
     * @param Cloner $output
     * @return AppResponse
     */
    public function success(Cloner $output): AppResponse
    {
        return new SGResponse(
            $this->stdio,
            $this->console,
            $this->input,
            $output->getAsyncInputs(),
            $output->getOutputs(),
            AppResponse::SUCCESS,
          AppResponse::DEFAULT_ERROR_MESSAGES[AppResponse::SUCCESS]
        );
    }

    public function response(int $errcode, string $errmsg = ''): AppResponse
    {
        $errmsg = !empty($errmsg)
            ? $errmsg
            : AppResponse::DEFAULT_ERROR_MESSAGES[$errcode] ?? '';

        return new SGResponse(
            $this->stdio,
            $this->console,
            $this->input,
            [],
            [],
            $errcode,
            $errmsg
        );
    }

    public function getProtocalId(): string
    {
        return StringUtils::normalizeString(get_class($this->message));
    }


}