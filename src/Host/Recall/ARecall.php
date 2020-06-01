<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Recall;

use Commune\Ghost\Memory\AbsRecall;
use Commune\Support\Parameter\ParamBuilder;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ARecall extends AbsRecall
{
    abstract public static function __scopes(): array;

    abstract public static function __params(ParamBuilder $builder): ParamBuilder;

}