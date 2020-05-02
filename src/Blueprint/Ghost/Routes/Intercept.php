<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Routes;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Routing\Hearing;
use Commune\Blueprint\Ghost\Routing\Retracing;
use Commune\Blueprint\Ghost\Routing\Redirecting;

/**
 * Current Context 的流程被另一个 Context 拦截.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Context $interceptor  拦截者.
 */
interface Intercept extends
    Route,
    Hearing,
    Retracing,
    Redirecting
{
}