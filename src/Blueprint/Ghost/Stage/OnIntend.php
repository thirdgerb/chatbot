<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Stage;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Routing\Backward;
use Commune\Blueprint\Ghost\Routing\Fallback;
use Commune\Blueprint\Ghost\Routing\Redirect;


/**
 * 在 A Context Stage 通过意图命中了 B Context Stage
 *
 * 会用 B Context Stage 的 onIntend 方法调用 A Context 对应的 Dialog
 *
 * 本质上还是在操作 A Context
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Context $intending
 */
interface OnIntend extends Stage
{
    public function redirect() : Redirect;

    public function fallback() : Fallback;

    public function backward() : Backward;
}