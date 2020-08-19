<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Support;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HeedFallbackUtils
{

    public static function makeStrategyId(
        string $intent,
        string $contextName = null,
        string $stageName = null
    ) : string
    {
        return md5("intent:$intent:context:$contextName:stage:$stageName");
    }

    public static function makeAllStrategyIds(
        string $intent,
        string $contextName,
        string $stageName
    ) : array
    {
        $intentId = self::makeStrategyId($intent);
        $contextId = self::makeStrategyId($intent, $contextName);
        $stageId = self::makeStrategyId($intent, $contextName, $stageName);
        return [
            $stageId,
            $contextId,
            $intentId
        ];
    }
}