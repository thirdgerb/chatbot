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

/**
 * 服务端的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name                  平台名称
 *
 * @property-read string $concrete              Platform 的实现.
 * @property-read array $config                 Kernel 的 config 参数的配置.
 *
 * @property-read array $providers
 * @property-read array $options
 *
 * @property-read string|null $bootShell        平台初始化时要启动的 Shell
 * @property-read bool $bootGhost               平台初始化时要启动的 Ghost
 *
 */
interface PlatformConfig extends Option
{

}