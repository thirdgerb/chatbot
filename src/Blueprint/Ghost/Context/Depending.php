<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;

use Commune\Blueprint\Ghost\Ucl;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Depending
{

    public function on(
        string $name,
        string $query = '',
        string $validator = null
    ) : Depending;

    public function onContext(
        string $name,
        Ucl $ucl,
        string $validator = null
    ) : Depending;

    public function onContextAttr(
        string $name,
        Ucl $ucl,
        string $attrName,
        string $validator = null
    ) : Depending;
}