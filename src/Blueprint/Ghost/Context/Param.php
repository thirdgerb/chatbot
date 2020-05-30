<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Param
{
    /**
     * 字段名
     * @return string
     */
    public function getName() : string;

    /**
     * @return bool
     */
    public function isNullable() : bool;

    /**
     * @return string[]
     */
    public function getTypeHints() : array;

    /**
     * @param mixed $value
     * @param string|null $type 可指定合法的 type
     * @return mixed
     */
    public function parse($value, string $type = null);

    /**
     * @param $value
     * @return null|string  返回合法的 typeHint
     */
    public function validate($value) : ? string;

    /**
     * 默认值
     * @return mixed
     */
    public function getDefault();
}