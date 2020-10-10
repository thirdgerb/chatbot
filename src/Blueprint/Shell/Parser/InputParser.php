<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell\Parser;

use Commune\Protocols\HostMsg;


/**
 * 输入消息的转义.
 *
 * 例如输入的是图片消息, 将图片转存到 storage, 然后再替换使用的 ID
 * 这样可以保证所有的 shell 都能共用这个图片.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface InputParser
{
    /**
     * @param HostMsg $message
     * @return HostMsg
     */
    public function __invoke(HostMsg $message) : HostMsg;


}