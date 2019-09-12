<?php


namespace Commune\Chatbot\App\Components\Predefined\Dialogue;

use Commune\Chatbot\App\Intents\MessageIntent;

/**
 * 序数词意图, 通常用于选项.
 *
 * @property-read int[]|null $ordinal 请问要选第几个
 */
class OrdinalInt extends MessageIntent
{
    const SIGNATURE = 'ordinal {ordinal : 序数} ';
    const DESCRIPTION = '序数';
    const ORDINAL_VAR = 'ordinal';


    const CASTS = [
        'ordinal' => 'int[]',
    ];

    public static function getContextName(): string
    {
        return 'dialogue.ordinal';
    }

}