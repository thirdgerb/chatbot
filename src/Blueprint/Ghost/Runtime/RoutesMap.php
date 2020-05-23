<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 单轮对话结束时, 对下一轮对话进行主动响应的路由图.
 * 上下文会根据路由的情况, 选择进入指定的 ucl 进行响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface RoutesMap extends ArrayAndJsonAble
{

    public function hearingIntents() : array;

    public function hearingWildcardIntents() : array;
}