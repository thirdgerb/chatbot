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

use Commune\Support\Option\AbsMeta;
use Commune\Ghost\Support\ContextUtils;
use Commune\Ghost\Context\IContextDef;
use Commune\Support\Option\Wrapper;

/**
 * Context 配置的元数据.
 * 用于定义各种 Context
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $name      当前配置的 ID
 * @property string $title     标题
 * @property string $desc      简介
 * @property string $wrapper   目标 Wrapper 的类名. 允许用别名.
 * @see Aliases
 *
 * @property array $config     wrapper 对应的配置.
 */
class ContextMeta extends AbsMeta implements DefMeta
{

    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'wrapper' => '',
            'config' => [],
        ];
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
        // 从设计理念上看, Context Wrapper 可以指定为 CodableContext , 情况特殊.
        return is_a($wrapper, $defType = Wrapper::class, TRUE)
            ? null
            : static::class . " wrapper should be subclass of $defType, $wrapper given";
    }


}