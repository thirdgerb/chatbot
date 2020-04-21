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


/**
 * 服务端的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name                  平台名称
 *
 * @property-read string|null $withShell        平台初始化时要启动的 Shell
 * @property-read string|null $withGhost        平台初始化时要启动的 Ghost
 *
 */
interface PlatformConfig
{

}