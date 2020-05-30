<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Codable;

use Commune\Blueprint\Ghost\Context\ContextOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DefineConfig
{

    const DEFINE_CONFIG_FUNC = '__config';

    /**
     * @return ContextOption
     */
    public static function __config() : ContextOption;
}