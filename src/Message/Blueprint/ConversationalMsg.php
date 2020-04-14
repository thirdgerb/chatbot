<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ConversationalMsg extends Message
{
    /**
     * 选项.
     * @return string[]
     */
    public function getSuggestions() : array;

    /**
     * @return string[]
     */
    public function getAnswers() : array;

    /**
     * 默认答案
     * @return string[]
     *
     * [
     *  index => answer
     * ]
     */
    public function getDefaultAnswers() : array;

    /**
     * 最大答案数. 0 以下表示不限.
     * @return int
     */
    public function getMaxChoiceCount() : int;

    /**
     * @return bool
     */
    public function isNullable() : bool;

    /**
     * @return bool
     */
    public function allowAny() : bool;
}