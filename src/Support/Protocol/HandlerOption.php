<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Protocol;

use Commune\Support\Option\AbsOption;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $handler       Handler 的 abstract, 用于 container 生成实例.
 * @property-read string[] $filters     匹配 handler 的过滤规则. 为空表示全匹配. 允许正则/通配符/字符串精确匹配.
 * @property-read array $params         生成 handler 时允许传入的参数.
 */
class HandlerOption extends AbsOption
{
    const IDENTITY = 'handler';

    public static function stub(): array
    {
        return [
            'handler' => '',
            'filters' => [],
            'params' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['handler']) ?? parent::validate($data);
    }
}