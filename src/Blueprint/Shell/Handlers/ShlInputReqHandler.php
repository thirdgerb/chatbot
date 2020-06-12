<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell\Handlers;

use Commune\Blueprint\Shell\Requests\ShlInputRequest;
use Commune\Blueprint\Shell\Responses\ShlInputResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlInputReqHandler
{

    public function __invoke(ShlInputRequest $request) : ShlInputResponse;
}