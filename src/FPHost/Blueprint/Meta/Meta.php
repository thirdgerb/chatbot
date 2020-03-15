<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint\Meta;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * 所有元数据的定义.
 * 这些元数据, 可以注册到机器人的思维中, 并且允许超级管理员动态进行编辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $type 元数据类型
 * @property-read string $id 元数据 ID
 * @property-read string $wrapper 元数据包装器.
 * @property-read array $config 元数据具体配置.
 * @property-read bool $isMutable 是否可修改
 */
interface Meta extends ArrayAndJsonAble
{
    /**
     * 从 Meta 生成为 wrapper
     * @return Wrapper
     */
    public function toWrapper() : Wrapper;
}