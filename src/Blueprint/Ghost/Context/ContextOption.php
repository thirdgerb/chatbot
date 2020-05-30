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

use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $title
 * @property-read string $desc
 *
 * @property-read int|null $priority
 * @property-read array|null $asIntent
 *
 * @property-read null|string[] $memoryScopes
 *
 * @property-read null|array $queryDefaults
 * @property-read null|array $paramDefaults
 *
 * @property-read null|string[] $dependingNames
 * @property-read null|string[] $entityNames
 *
 * @property-read null|array $comprehendPipes
 *
 * @property-read null|string $firstStage
 * @property-read null|string $onCancel
 * @property-read null|string $onQuit
 * @property-read null|string[] $stageRoutes
 * @property-read null|string[] $contextRoutes
 */
class ContextOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'title' => '',
            'desc' => '',

            'priority' => null,
            'asIntent' => null,

            'memoryScopes' => null,
            'queryDefaults' => null,
            'paramDefaults' => null,

            'dependingNames' => null,
            'entityNames' => null,

            'comprehendPipes' => null,

            'firstStage' => null,
            'onCancel' => null,
            'onQuit' => null,
            'stageRoutes' => null,
            'contextRoutes' => null,
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}