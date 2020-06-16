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
use Commune\Support\Protocal\ProtocalOption;

/**
 * 对话机器人消息层的配置.
 * 输出层的任务是将输入或输出的消息进行统一化.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id                id, 需要纯字母加数字
 * @property-read string $name              Shell 的名称
 *
 * ## 服务注册
 *
 * @property-read array $configProviders                    需要绑定的配置服务.
 * @property-read array $procProviders                      需要绑定的进程级服务.
 * @property-read array $reqProviders                       需要绑定的请求级服务
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
 * @property-read ProtocalOption[] $requestHandlers     请求的处理协议
 * [
 *     [
 *         'protocal' => Protocal::class,
 *         'filters' => ['*'],
 *         'handler' => Handler::class,
 *         'params' => [ param1, param2...]
 *     ],
 * ]
 *
 * @property-read ProtocalOption[] $apiHandlers         输入消息的处理协议.
 * @property-read ProtocalOption[] $inputParsers        输入消息的处理协议.
 * @property-read ProtocalOption[] $outputRenderers     输出消息的处理协议
 *
 * ## Session 配置
 *
 * @property-read int $sessionExpire                        会话的过期时间, 秒
 * @property-read int $sessionLockerExpire                  锁的时间
 */
interface ShellConfig extends Option
{

}