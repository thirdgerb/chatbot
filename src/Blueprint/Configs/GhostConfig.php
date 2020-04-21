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
 * 机器人多轮对话内核的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $ghostName             Ghost 的名称
 *
 * # Services
 * @property-read array $providers              需要绑定的服务.
 *
 * @property-read string[] $sceneContextNames   场景对应的根路径. 第一个是默认的.
 */
interface GhostConfig
{

}