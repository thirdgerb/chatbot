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
 * @property-read string $id                    平台名称
 * @property-read string $concrete              Platform 的实现.
 * @property-read string $adapter               平台上对输入输出进行处理的适配器.
 *
 * @property-read string|null $bootShell        平台初始化时要启动的 Shell
 * @property-read bool $bootGhost               平台初始化时要启动的 Ghost
 *
 * @property-read array $providers
 * @property-read array $options
 *
 *
 */
interface PlatformConfig extends Option
{

}