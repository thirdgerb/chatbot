<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Configs\Nest;

use Commune\Support\Option\AbsOption;
use Commune\Support\Protocal\Protocal;


/**
 * Session 协议的配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $group         协议所属的分组
 * @property-read string $protocal      协议的类名
 * @property-read string $handler       Handler 的类名
 * @property-read array $params         Handler 构造器可以补充的参数, 依赖注入.
 */
class ProtocalOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'group' => '',
            'protocal' => '',
            'handler' => '',
            'params' => [],
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        if (empty($data['group'])) {
            return 'group is required';
        }

        if (empty($data['protocal'])) {
            return 'protocal is required';
        }

        $protocal = $data['protocal'] ?? '';
        if (!is_a($protocal, $class = Protocal::class, TRUE)) {
            return "protocal field should be subclass of $class, $protocal given";
        }

        return parent::validate($data);
    }

    public static function relations(): array
    {
        return [];
    }

}