<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Trans;

use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $temp
 * @property-read string $locale
 * @property-read string $domain
 * @property-read bool $intl
 */
class TransOption extends AbsOption
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => '',
            'temp' => '',
            'locale' => '',
            'domain' => '',
            'intl' => false,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}