<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Support;

use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ContextTypeUtils
{

    public static function normalizeContextName(string $contextName) : string
    {
        $contextName = str_replace('\\', '/', $contextName);
        return StringUtils::normalizeString($contextName);
    }

    /**
     * Ucl 标准名称.
     * @param string $ucl
     * @return bool
     */
    public static function isValidUcl(string $ucl) : bool
    {
        $pattern = '/^([a-z][a-z0-9\.]+)(#[a-z][a-z0-9\.]*){0,1}(\?\{.*\}){0,1}$/';
        $matched = [];

        $result = (bool) preg_match($pattern, $ucl, $matched);
        return $result;
    }

}