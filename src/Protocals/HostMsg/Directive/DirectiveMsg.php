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
 * 指令类的消息. 由服务端下发给客户端.
 * 而服务端默认机制是不处理的.
 *
 * 通常由客户端根据本地状态变更, 主动上报给服务端, 告知当前可以运行的指令;
 * 而服务端接受到消息时, 再判断是否是客户端预定义的指令, 从而将之下发给客户端.
 *
 * 在异构机器人的场景中尤其有用, 是一种指令发现/远程操作 的机制.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $shell     指令从属的 shell.
 * @property-read string $name      指定的名称.
 *
 * @property-read string $trigger   触发指定的输入信息 (文本).
 * @property-read array $payload    指令对应的参数 (如果有的话).
 *
 */
interface DirectiveMsg extends HostMsg
{

}