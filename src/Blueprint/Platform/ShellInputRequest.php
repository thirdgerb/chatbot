<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform;

use Commune\Protocals\Intercom\GhostInput;
use Commune\Protocals\Intercom\ShellInput;
use Commune\Protocals\Intercom\ShellMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellInputRequest
{

    /**
     * @return ShellInput
     */
    public function getInput() : ShellInput;

    /**
     * @return Response
     */
    public function reject() : Response;

    /**
     * @param int $code
     * @param string $message
     * @return Response
     */
    public function fail(int $code, string $message) : Response;

    /**
     * @param ShellMsg[] $messages
     * @return Response
     */
    public function stop(array $messages) : Response;

    /**
     * @param GhostInput $ghostInput
     * @return Response
     */
    public function succeed(GhostInput $ghostInput) : Response;

}