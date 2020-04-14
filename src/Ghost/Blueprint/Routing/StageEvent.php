<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Routing;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Operator\Operator;

/**
 * 切换当前 Stage 的事件.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StageEvent
{
    public function onIntend(Context $context) : Operator;

    /*------- 自身 ------*/

    public function onActivate() : Operator;

    public function onHeed() : Operator;

    /*------- 回调 ------*/

    public function onReject(Context $context) : Operator;

    public function onCancel(Context $context) : Operator;

    public function onQuit(Context $context) : Operator;

    public function onFulfill(Context $context) : Operator;

    public function onWake(Context $context) : Operator;


}