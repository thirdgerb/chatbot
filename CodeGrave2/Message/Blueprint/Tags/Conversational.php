<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Tags;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Conversational
{
    /**
     * 选项.
     * @return array
     */
    public function getSuggestions() : array;

    /**
     * 默认选项
     * @return int|string
     */
    public function getDefaultIndex();
}