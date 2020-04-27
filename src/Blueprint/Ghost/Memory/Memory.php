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
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 用静态方法来获取一个记忆单元.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Memory extends \ArrayAccess, ArrayAndJsonAble, Memorable
{

    /**
     * 获取当前记忆单元的 Id
     * @return string
     */
    public function getId() : string;

    /**
     * @param Cloner $cloner
     * @return static
     */
    public static function find(Cloner $cloner) : Memory;

    /**
     * Memory 的默认值.
     * @return array
     */
    public static function stub() : array;

    /**
     * 记忆名称.
     * @return string
     */
    public static function getMemoryName() : string;

    /**
     * 在上下文中生成一个唯一的 Id
     * @param Cloner $cloner
     * @return string
     */
    public static function makeId(Cloner $cloner) : string;

}