<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg\Directive;

use Commune\Protocals\HostMsg;


/**
 * Directive 指令集的定义. 通常由客户端上报给服务端.
 * 当然, 服务端也可以自己预定义好指令集, 并由客户端用别的方式来触发.
 * 那一类机制暂时不考虑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $shell         指令从属的 shell.
 * @property-read string $name          指令集的名称.
 *
 * @property-read DirectiveDef[] $defs  指令集中定义的命令.
 *
 * @property-read int $mode             指令集默认的响应模式. 本系统预留预定义的若干种. 也可以服务端和客户端预先约定.
 */
interface DirectiveSet extends HostMsg
{
    // 指令集持续, 直到被新指定的指令集取代.
    const MODE_LAST = 0;
    // 指令集是一次性的, 一旦命中后就失效, 等待新的指令来取代.
    const MODE_ONCE = 1;
    // 仅仅生效于下一次符合预期类型的消息.
    const MODE_NEXT = 2;
}