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

use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $title                     标题
 * @property-read string $desc                      简介
 *
 * @property-read int $priority                     语境的默认优先级
 *
 * @property-read array $queryParams
 *
 * @property-read string[] $memoryScopes
 * @property-read array $memoryParams
 *
 * @property-read string[] $dependingNames
 * @property-read string[] $entityNames
 *
 * @property-read null|array $comprehendPipes
 *
 * @property-read null|string $onCancel
 * @property-read null|string $onQuit
 *
 * @property-read string[] $stageRoutes
 * @property-read string[] $contextRoutes
 *
 */
class CodeContextOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'title' => '',
            'desc' => '',
            'priority' => 0,

            'queryParams' => [],

            'memoryScopes' => [],
            'memoryParams' => [],

            'dependingNames' => [],
            'entityNames' => [],

            'comprehendPipes' => null,
            'onCancel' => null,
            'onQuit' => null,
            'stageRoutes' => [],
            'contextRoutes' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}