<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Configs\Nest;

use Commune\Support\Option\Option;


/**
 * Session 协议的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $group         协议所属的分组
 * @property-read string $protocal      协议的类名
 * @property-read string $handler       Handler 的类名
 * @property-read array $params         Handler 构造器可以补充的参数, 依赖注入.
 */
interface ProtocalOption extends Option
{
}