<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg\Tags;


/**
 * 表示是与 markdown 兼容的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Markdown
{
    public function toMarkdownText() : string;
}