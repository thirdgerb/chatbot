<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Message;

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 必须避免上下级相互持有, 陷入死循环. 通常序列化有深度限制.
 *
 * @property-read string $type                              消息类型
 *  对应 Message::getType()
 *
 * @property-read array $attrs                              消息属性.
 *
 * @property-read array|Transfer[][]|Transfer[] $relations  关联对象.
 *  都可以反序列化为 Meta, 进一步变成 Message. 也是一种 Fractal
 *
 * @property-read string[] $protocals                       消息实现的协议.
 *
 */
interface Transfer extends ArrayAndJsonAble
{
    public function toMessage() : Message;
}