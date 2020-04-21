<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform\Pipeline;

use Commune\Blueprint\Platform\Shell\OutputReq;
use Commune\Blueprint\Platform\Shell\OutputRes;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellOutputPipe
{
    const VIA = 'handle';

    public function handle(OutputReq $req, callable $next) : OutputRes;

}