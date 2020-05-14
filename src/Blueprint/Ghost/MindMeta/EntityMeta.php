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

use Commune\Support\Option\AbsOption;
use Commune\Support\Option\Wrapper;
use Commune\Support\Utils\StringUtils;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name          entity 名称.
 * @property-read string $title         标题
 * @property-read string $desc          摘要
 * @property-read string[] $values      entity 的值
 * @property-read string[] $synonyms    同义词的名称.
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
            'synonyms' => [],
            'blacklist' => [],
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        if (!StringUtils::isNotEmptyStr($data['name'] ?? null)) {
            return "name is required";
        }

        if (empty($data['values'])) {
            return 'entity values are required';
        }

        $name = $data['name'] ?? '';

        if (!TypeUtils::isValidEntityName($name)) {
            return "entity name $name is invalid";
        }

        return parent::validate($data);
    }

    public static function relations(): array
    {
        return [];
    }

    public function getWrapper(): Wrapper
    {
        // TODO: Implement getWrapper() method.
    }


}