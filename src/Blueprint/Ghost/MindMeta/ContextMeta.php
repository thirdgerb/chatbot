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

use Commune\Support\Alias\TAliases;
use Commune\Support\Option\AbsMeta;
use Commune\Ghost\Support\ContextUtils;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Ghost\Context\Prototype\IContextDef;

/**
 * Context 配置的元数据.
 * 用于定义各种 Context
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name      当前配置的 ID
 * @property-read string $title     标题
 * @property-read string $desc      简介
 * @property-read string $wrapper   目标 Wrapper 的类名. 允许用别名.
 * @see Aliases
 *
 * @property-read array $config     wrapper 对应的配置.
 */
class ContextMeta extends AbsMeta
{
    use TAliases;

    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'wrapper' => self::getAliasOfOrigin(IContextDef::class),
            'config' => [],
        ];
    }

    public function __get_wrapper() : string
    {
        return self::getOriginFromAlias($this->_data['wrapper'] ?? '');
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        $contextName = $data['name'] ?? '';
        if (!ContextUtils::isValidContextName($contextName)) {
            return 'context name is invalid';
        }

        return parent::validate($data);
    }

    public static function validateWrapper(string $wrapper): ? string
    {
        $defType = ContextDef::class;
        return is_a($wrapper, $defType, TRUE)
            ? null
            : static::class . " wrapper should be subclass of $defType, $wrapper given";
    }


}