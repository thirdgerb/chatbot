<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint;

use Commune\Ghost\Blueprint\GhostConfig;
use Commune\Shell\Blueprint\ShellConfig;
use Commune\Support\Structure;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * Properties
 *
 * @property-read string $chatbotName  机器人的名字
 * @property-read bool $debug
 *
 * Config
 *
 * @property-read GhostConfig $ghost  机器人对话管理内核的配置
 * @property-read ShellConfig[] $shells 机器人所有可用平台的配置.
 *
 * Kernels
 *
 * @property-read string $apiKernel
 * @property-read string $callbackKernel
 * @property-read string $userKernel
 *
 * Defaults
 *
 * @property-read string[] $bootstrappers
 * @property-read string[] $baseServices
 */
class ChatAppConfig extends Structure
{
    const IDENTITY = 'chatbotName';

    protected static $associations = [
        'ghost' => GhostConfig::class,
        'shells[]' => ShellConfig::class,
    ];

    public static function stub(): array
    {
        return [

            // 机器人的名称
            'chatbotName' => '',

            // 是否开启 debug 模式
            'debug' => true,

            // 机器人的灵魂
            'ghost' => [],

            // 机器人的各种壳
            'shells' => [],


            // 以下为默认配置, 通常不用修改.

            'apiKernel' => '',
            'callbackKernel' => '',
            'userKernel' => '',
            'bootstrappers' => [],
            'baseServices' => [],


        ];
    }

    /**
     * @var string[]
     */
    private $shellNames = null;

    public function getShellNames() : array
    {
        if (isset($this->shellNames)) {
            return $this->shellNames;
        }

        $this->shellNames = [];
        foreach ($this->shells as $shell) {
            $this->shellNames[] = $shell->name;
        }
        return $this->shellNames;
    }

}