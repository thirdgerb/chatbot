<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\HostMsg\Convo;

use Commune\Protocols\HostMsg\ConvoMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MediaMsg extends ConvoMsg
{
    /**
     * 对资源的基本描述. 用于 renderer 去筛选.
     * @return string
     */
    public function getResource() : string;

    /**
     * 对资源的文字描述, 有些场景可以用来做降级.
     * @return string
     */
    public function getText(): string;
}
