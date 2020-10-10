<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Configs;

use Commune\Support\Option\Option;
use Commune\Support\Protocol\ProtocolOption;

/**
 * 对话机器人消息层的配置.
 * 输出层的任务是将输入或输出的消息进行统一化.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id                                id, 需要纯字母加数字
 * @property-read string $name                              Shell 的名称
 *
 * ## 服务注册
 *
 * @property-read array $providers                          需要绑定的服务
 *
 * [   ServiceProvider::class,
 *      ServiceProvider1::class => [ configs ]]
 *
 *
 * ## 组件注册
 *
 * @property-read array $components                         默认绑定的组件.
 *
 *  [   ComponentClass::class,
 *      ComponentClass1::class => [configs] ]
 *
 * ## 配置注册
 *
 * @property-read array $options                            默认绑定的 Option 单例
 *
 *  [   OptionClass::class,
 *      OptionClass1::class => [ configs ] ]
 *
 * ## 协议定义.
 *
 * @property-read ProtocolOption[] $Protocols               Shell 处理协议的配置.

 * ## Session 配置
 *
 * @property-read int $sessionExpire                        会话的过期时间, 秒
 */
interface ShellConfig extends Option
{

}