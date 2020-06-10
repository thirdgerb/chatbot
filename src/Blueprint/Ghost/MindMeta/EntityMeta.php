<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindMeta;

use Commune\Ghost\IMindDef\IEntityDef;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name          entity 名称.
 * @property-read string $title         标题
 * @property-read string $desc          摘要
 * @property-read string[] $values      entity 的值
 * @property-read string[] $blacklist   黑名单.
 */
class EntityMeta extends AbsOption implements DefMeta
{
    const IDENTITY = 'name';


    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'values' => [],
            'blacklist' => [],
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        if (empty($data['name'])) {
            return "name is required";
        }

        if (empty($data['values'])) {
            return 'entity values are required';
        }

        $name = $data['name'] ?? '';
        if (!ContextUtils::isValidEntityName($name)) {
            return "entity name $name is invalid";
        }


        return parent::validate($data);
    }

    public static function relations(): array
    {
        return [];
    }

    public function toWrapper(): Wrapper
    {
        return new IEntityDef($this);
    }

}