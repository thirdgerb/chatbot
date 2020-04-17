<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Definition;

use Commune\Support\Struct\Wrapper;

/**
 * 某种逻辑配置的定义. 可以通过 Meta 数据生成出来.
 * 系统对 Meta 数据的加载, 决定了自己会有哪些逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Def extends Wrapper
{
    /**
     * Def 名称
     * @return string
     */
    public function getName() : string;

    /**
     * 名称匹配 (考虑到 反斜杠/大小写 之类特殊规则的一致化)
     * @param string $name
     * @return bool
     */
    public function nameEquals(string $name) : bool;

    /**
     * 文字标题
     * @return string
     */
    public function getTitle() : string;

    /**
     * 详细介绍.
     * @return string
     */
    public function getDescription() : string;

}