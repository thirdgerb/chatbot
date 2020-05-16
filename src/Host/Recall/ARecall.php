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

use Commune\Blueprint\Ghost\Context\ParamBuilder;
use Commune\Ghost\Memory\RecallPrototype;
use Commune\Ghost\Support\ContextUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ARecall extends RecallPrototype
{
    public static function recallName(): string
    {
        return ContextUtils::normalizeMemoryName(static::class);
    }

    abstract public static function getScopes(): array;


    abstract public static function getParamOptions(ParamBuilder $builder): ParamBuilder;



}