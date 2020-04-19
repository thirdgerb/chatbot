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

use Commune\Framework\Blueprint\Intercom\ShellMessage;
use Commune\Shell\Contracts\ShellResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RSResponse implements ShellResponse
{

    protected $buffered = [];

    public function buffer(array $messages): void
    {
        $this->buffered = array_merge($this->buffered, $messages);
    }

    protected function write(ShellMessage $message) : void
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