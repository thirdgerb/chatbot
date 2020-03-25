<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Tag;

use Psr\Log\LogLevel;

/**
 * 文字类消息
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Verbal
{

    public function getTrimmedText() : string;

    /**
     * @see LogLevel
     * @return string
     */
    public function getLevel() : string;
}