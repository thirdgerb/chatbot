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
     * @param array|null $stopWords   为 null 表示使用默认的 stopWords
     * @return array
     */
    public function tokenize(string $sentence, array $stopWords = null) : array;

}