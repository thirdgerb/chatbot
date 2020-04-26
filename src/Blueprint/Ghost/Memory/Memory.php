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
use Commune\Blueprint\Ghost\ClonerScope;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 用静态方法来获取一个记忆单元.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Memory extends \ArrayAccess, ArrayAndJsonAble
{

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
     * 自动生成作用域的 id
     *
     * @param ClonerScope $scope
     * @return string
     */
    public static function makeRecollectionId(ClonerScope $scope) : string;

    public static function getRecollectionName() : string;
}