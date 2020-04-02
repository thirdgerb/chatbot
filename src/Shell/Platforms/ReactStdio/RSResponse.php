<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Platforms\ReactStdio;

use Commune\Framework\Blueprint\Intercom\ShellMsg;
use Commune\Shell\Contracts\ShlResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RSResponse implements ShlResponse
{

    protected $buffered = [];

    public function buffer(array $messages): void
    {
        $this->buffered = array_merge($this->buffered, $messages);
    }

    protected function write(ShellMsg $message) : void
    {

    }

    public function sendResponse(): void
    {
        // TODO: Implement sendResponse() method.
    }

    public function sendRejectResponse(): void
    {
        // TODO: Implement sendRejectResponse() method.
    }

    public function sendFailureResponse(\Exception $e = null): void
    {
        // TODO: Implement sendFailureResponse() method.
    }


}