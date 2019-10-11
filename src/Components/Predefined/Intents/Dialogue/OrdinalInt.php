<?php


namespace Commune\Components\Predefined\Intents\Dialogue;

use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Support\Utils\StringUtils;

/**
 * 序数词意图, 通常用于选项.
 *
 * @property-read int[]|string[] $ordinal 请问要选第几个
 */
class OrdinalInt extends MessageIntent
{
    const SIGNATURE = 'ordinal {ordinal : 序数} ';
    const DESCRIPTION = '序数';
    const ORDINAL_VAR = 'ordinal';

    const REGEX = [
        ['/^第(\d+)个/', 'ordinal']
    ];

    const CASTS = [
        'ordinal' => 'int[]',
    ];

    public static function getContextName(): string
    {
        return 'dialogue.ordinal';
    }

    public function __getOrdinal() : array
    {
        $choices = $this->getAttribute('ordinal');
        if (empty($choices) || !is_array($choices)) {
            return [];
        }

        return array_map(function($choice){
            if (is_string($choice)) {
                $choice = StringUtils::normalizeString($choice);
                $choice = StringUtils::simpleCharToInt($choice) ?? $choice;
            }

            if (is_numeric($choice)) {
                return intval($choice);
            }

            return $choice;

        }, $choices);
    }

}