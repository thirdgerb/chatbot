<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef;

use Commune\Blueprint\Ghost\MindReg\SynonymReg;


/**
 * Entity 词典配置.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EntityDef extends Def
{

    /**
     * 在字符串中匹配出词典的值.
     *
     * @param string $text
     * @param SynonymReg $reg
     * @return string[]
     */
    public function match(string $text, SynonymReg $reg) : array;

    /**
     * 获得词典的默认值.
     * @return string[]
     */
    public function getValues() : array;

    /**
     * 词典的黑名单, 如果有命中则必然不是 Entity
     * @return string[]
     */
    public function getBlacklist() : array;

    /**
     * 获取默认值对应的同义词词典.
     * @return string[]
     */
    public function getSynonymNames() : array;

}