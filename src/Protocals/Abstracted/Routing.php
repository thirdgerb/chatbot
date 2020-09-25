<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Abstracted;


/**
 * 指定路由. 比命中意图更高级的方向确认.
 * 如果使用 NLP 作为对话管理, 则可以通过这种方式让 NLP 接过决策, 负责重定向.
 *
 * 这样, Stage 就成为了一种 Policy 的实现方式.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Routing
{

    public function setRedirection(string $routeName) : void;

    public function getRedirection() : ? string;

}