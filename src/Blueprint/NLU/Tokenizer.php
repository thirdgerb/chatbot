<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\NLU;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Tokenizer extends NLUService
{
    /**
     * @param string $sentence
     * @return string[] words
     */
    public function tokenize(string $sentence) : array;

}