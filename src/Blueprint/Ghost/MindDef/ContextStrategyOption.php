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
 * @property string|null $onCancel
 * @property string|null $onQuit
 * @property string|null $heedFallbackStrategy
 * @property string[] $stageRoutes
 * @property string[] $contextRoutes
 * @property string[]|null $comprehendPipes
 * @property string[] $auth
 */
class ContextStrategyOption extends AStruct
{
    public static function stub(): array
    {
        return [
            'auth' => [],
            'onCancel' => null,
            'onQuit' => null,
            'heedFallbackStrategy' => null,
            'comprehendPipes' => null,
            'stageRoutes' => [],
            'contextRoutes' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}