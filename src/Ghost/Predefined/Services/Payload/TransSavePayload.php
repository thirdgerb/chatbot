<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Predefined\Services\Payload;

use Commune\Support\Struct\AStruct;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property string[] $messages
 * @property string|null $domain
 * @property string|null $locale
 * @property bool|null $intl
 * @property bool|null $force
 */
class TransSavePayload extends AStruct
{
    public static function stub(): array
    {
        return [];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['messages'])
            ?? parent::validate($data);
    }

    public static function relations(): array
    {
        return [];
    }


}