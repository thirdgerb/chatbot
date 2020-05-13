<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Memory;

use Commune\Blueprint\Ghost\Cloner;

/**
 * 静态的回忆工具, 用静态方法来定义和获取记忆体.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recall extends Recollection
{

    /**
     * @param Cloner $cloner
     * @param string|null $id
     * @return static
     */
    public static function find(Cloner $cloner, string $id = null) : Recall;

    /**
     * 记忆的上下文, 用于生成唯一 ID.
     * @see Cloner\ClonerScope
     * @return string[]
     */
    public static function getScopes() : array;

    /**
     * 初始值.
     * @return array
     */
    public static function stub() : array;
}