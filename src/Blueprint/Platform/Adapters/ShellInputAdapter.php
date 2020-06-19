<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform\Adapters;

use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Platform\Adapter;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellInputAdapter extends Adapter
{

    public function getRequest() : ShellInputRequest;

    /**
     * @param ShellOutputResponse
     */
    public function sendResponse($response): void;
}