<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform;

use Commune\Support\Option\AbsOption;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id                    平台 id
 * @property-read string $name                  平台的名称. 
 * @property-read string $desc                  平台的简介. 
 * 
 * @property-read string $concrete              Platform 的实现.
 *
 * @property-read string|null $bootShell        平台初始化时要启动的 Shell
 * @property-read bool $bootGhost               平台初始化时要启动的 Ghost
 *
 * @property-read array $providers
 * @property-read array $options
 */
class IPlatformConfig extends AbsOption implements PlatformConfig
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'desc' => '',
            'concrete' => '',
            'bootShell' => null,
            'bootGhost' => false,
            'providers' => [],
            'options' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['id', 'concrete'])
            ?? parent::validate($data);
    }

    public function getTitle(): string
    {
        return $this->name;
    }
    
    public function getDescription(): string
    {
        return $this->desc;
    }
}