<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\Support;

use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class NLUUtils
{

    public static function isNotNatureLanguage(string $str) : bool
    {
        $str = trim($str);
        return StringUtils::isEmptyStr($str)
            || is_numeric($str)
            // 纯字符
            || preg_match('/^[\p{L}\p{P}\p{S}\p{Z}]\+$/', $str);
    }

}