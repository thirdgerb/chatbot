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

use Commune\Blueprint\Ghost\Mind\Definitions\ContextDef;
use Commune\Ghost\MindDef\IContextDef;
use Commune\Support\Option\AbsMeta;
use Commune\Support\Utils\TypeUtils;

/**
 * Context 配置的元数据.
 * 用于定义各种 Context
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name      当前配置的 ID
 * @property-read string $title     标题
 * @property-read string $desc      简介
 * @property-read string $wrapper   目标 Option 的类名. 允许用别名.
 * @see Aliases
 *
 * @property-read array $config     wrapper 对应的配置.
 *
 * @property-read string|null $editorContext    编辑配置的语境. 在多轮对话中编辑多轮对话逻辑.
 */
class ContextMeta extends AbsMeta
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => 'contextTitle',
            'desc' => 'contextDesc',
            'wrapper' => ContextDef::class,
            'config' => ContextDef::stub(),
            'editorContext' => null,
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        $contextName = $data['name'] ?? '';
        if (!TypeUtils::isValidContextName($contextName)) {
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