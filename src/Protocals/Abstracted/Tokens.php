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
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Tokens
{
    /**
     * @param string[] $tokens
     */
    public function addTokens(array $tokens) : void;

    /**
     * @return string[]
     */
    public function getTokens() : ? array;

    /**
     * @return bool
     */
    public function hasTokens() : bool;
}
