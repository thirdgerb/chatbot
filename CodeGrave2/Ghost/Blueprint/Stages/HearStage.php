<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Context;
use Commune\Ghost\Blueprint\Routing\Backward;
use Commune\Ghost\Blueprint\Routing\ContextRouter;
use Commune\Ghost\Blueprint\Routing\Hearing;
use Commune\Ghost\Blueprint\Routing\Staging;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface HearStage
{
    public function hearing() : Hearing;

    public function fallback() : ContextRouter;

    public function staging() : Staging;

    public function backward() : Backward;
}
