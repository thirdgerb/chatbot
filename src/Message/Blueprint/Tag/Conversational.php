<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Tag;


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
     * @return array
     */
    public function getAnswers() : array;

    /**
     * 默认答案
     * @return string[]
     *
     * [
         * index => answer
     * ]
     */
    public function getDefaultAnswers() : array;

    /**
     * 最大答案数. 0 以下表示不限.
     * @return int
     */
    public function getMaxChoiceCount() : int;

}