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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ValidateUtils
{

    public static function isArgInstanceOf($argument, string $class, bool $throw = true) : bool
    {
        $isA = TypeUtils::isA($argument, $class);

        if (!$isA && $throw) {
            $expect = $class;
            $actual = TypeUtils::getType($argument);
            throw new InvalidArgumentException(
                "expect $expect, $actual given"
            );
        }

        return $isA;
    }

}