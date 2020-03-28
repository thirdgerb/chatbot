<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Abstracted;

use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * 分词判断. 如果可以分词的话.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Tokenization extends ArrayAndJsonAble
{

    /*----- 分词 -----*/

    /**
     * 设置分词
     * @param string[] $tokens
     */
    public function setTokens(array $tokens) : void;

    /**
     * 获取分词
     * @return string[]
     */
    public function getTokens() : array;

}