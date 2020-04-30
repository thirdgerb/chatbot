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
use Commune\Blueprint\Ghost\Routing\Fallbacking;
use Commune\Blueprint\Ghost\Routing\Redirecting;

/**
 * 在 $current Context 下, 通过路由可能要跳转到 $self Context
 * 可以指定各种处理逻辑
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Context $current
 */
interface Intercept extends
    Route,
    Hearing,
    Fallbacking,
    Redirecting
{

}