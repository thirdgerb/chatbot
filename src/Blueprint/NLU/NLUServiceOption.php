<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\NLU;

use Commune\Support\Option\AbsOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id,
 * @property-read string $desc,
 * @property-read string $serviceAbstract,
 *
 * @property-read string|null $managerUcl       用对话管理该服务的对话地址.
 * @property-read int $priority
 */
class NLUServiceOption extends AbsOption
{
    const IDENTITY = 'id';

    const MIDDLE_PRIORITY = 10;

    public static function stub(): array
    {
        return [
            'id' => '',
            'desc' => '',
            'serviceAbstract' => '',
            'managerUcl' => null,
            'priority' => self::MIDDLE_PRIORITY,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}