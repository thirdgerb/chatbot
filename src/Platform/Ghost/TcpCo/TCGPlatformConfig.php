<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost\TcpCo;

use Commune\Platform\IPlatformConfig;

/**
 * 平台的 Host 配置. 提供给 HostConfig
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TCGPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => 'ghost',
            'concrete' => TCGPlatform::class,
            'adapter' => '',
            'bootShell' => null,
            'bootGhost' => true,
            'providers' => [],
            'options' => [
                TCGServerOption::class => [
                ],
            ],
        ];
    }

    /**
     * 默认不启动 Shell
     * @return null|string
     */
    public function __get_bootShell() : ? string
    {
        return null;
    }

    /**
     * 默认启动 Ghost
     * @return bool
     */
    public function __get_bootGhost() : bool
    {
        return true;
    }
}