<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Configs\HostConfig;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Configs\ShellConfig;
use Commune\Ghost\IGhostConfig;
use Commune\Platform\IPlatformConfig;
use Commune\Shell\IShellConfig;
use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $name
 *
 * @property-read array $providers              所有 组件共享的服务配置.
 * @property-read array $options
 *
 * # 关联配置
 * @property-read GhostConfig $ghost            Ghost 的配置
 * @property-read ShellConfig[] $shells         Shell 的配置
 * @property-read PlatformConfig[] $platforms   Platform 的配置
 */
class IHostConfig extends AbsOption implements HostConfig
{

    /**
     * @var null|ShellConfig[]
     */
    protected $_shellMap;

    /**
     * @var null|PlatformConfig[]
     */
    protected $_platformMap;

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'providers' => [],
            'options' => [],
            'ghost' => [],
            'shells' => [],
            'platforms' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'ghost' => IGhostConfig::class,
            'shells[]' => IShellConfig::class,
            'platforms[]' => IPlatformConfig::class,
        ];
    }

    public function getGhostConfig(): GhostConfig
    {
        return $this->ghost;
    }

    public function getShellConfig(string $shellName): ? ShellConfig
    {
        if (!isset($this->_shellMap)) {
            $this->_shellMap = [];
            foreach ($this->shells as $shellConfig) {
                $id = $shellConfig->id;
                $this->_shellMap[$id] = $shellConfig;
            }
        }

        return $this->_shellMap[$shellName] ?? null;
    }

    public function getPlatformConfig(string $platformId): ? PlatformConfig
    {
        if (!isset($this->_platformMap)) {
            $this->_platformMap = [];
            foreach ($this->platforms as $platformConfig) {
                $id = $platformConfig->id;
                $this->_platformMap[$id] = $platformConfig;
            }
        }

        return $this->_platformMap[$platformId] ?? null;
    }


}