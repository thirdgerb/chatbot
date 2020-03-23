<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Mind;


/**
 * 记忆实体的类型
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MemoryDef
{
    /**
     * 记忆单元的名称.
     *
     * @return string
     */
    public function memoryName() : string;

    /**
     * 作用域
     *
     * @return array
     */
    public function scopes() : array;

    /**
     * 默认值
     *
     * @return array
     */
    public function defaultValues() : array;

    /**
     * 是否是长程记忆.
     * @return bool
     */
    public function isLongTerm() : bool;

    public function makeId(Scope $scope) : string;
}