<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef;

use Commune\Support\Struct\AStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string|null $onCancel
 * @property-read string|null $onQuit
 * @property-read string|null $heedfallbackStrategy
 * @property-read string[] $stageRoutes
 * @property-read string[] $contextRoutes
 * @property-read string[]|null $comprehendPipes
 * @property-read string[] $auth
 */
class ContextStrategyOption extends AStruct
{
    public static function stub(): array
    {
        return [
            'onCancel' => null,
            'onQuit' => null,
            'heedFallbackStrategy' => null,
            'comprehendPipes' => null,
            'stageRoutes' => [],
            'contextRoutes' => [],
            'auth' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}