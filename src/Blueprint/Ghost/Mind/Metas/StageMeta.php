<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Mind\Metas;

use Commune\Blueprint\Ghost\Mind\Definitions\StageDef;
use Commune\Ghost\MindDef\IStageDef;
use Commune\Support\Option\AbsMeta;


/**
 * Stage 的元数据.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageMeta extends AbsMeta
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => 'contextTitle',
            'desc' => 'contextDesc',
            'wrapper' => IStageDef::class,
            'config' => IStageDef::stub(),
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validateWrapper(string $wrapper): ? string
    {
        $defType = StageDef::class;
        return is_a($wrapper, $defType, TRUE)
            ? null
            : static::class . " wrapper should be subclass of $defType, $wrapper given";
    }

}