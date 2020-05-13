<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Mind\Metas;

use Commune\Ghost\Mind\Defs\IMemoryDef;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Wrapper;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name      当前配置的 ID
 * @property-read string $title     标题
 * @property-read string $desc      简介
 * @property-read string[] $scopes  记忆的作用域.
 * @property-read array $defaults   记忆的默认值.
 */
class MemoryMeta extends AbsOption implements DefMeta
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => 'contextTitle',
            'desc' => 'contextDesc',
            'scopes' => [],
            'defaults' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        $name = $data['name'] ?? '';
        if (TypeUtils::isValidMemoryName($name)) {
            return "memory name $name is invalid";
        }

        return parent::validate($data);
    }

    public function getWrapper(): Wrapper
    {
        return IMemoryDef::wrap($this);
    }


}