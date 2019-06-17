<?php


namespace Commune\Chatbot\App\Components\Configurable\Configs;


use Commune\Support\Option;

/**
 * @property-read string $act action的名称.
 * @property-read mixed $value
 *
 */
class ActionConfig extends Option
{
    const IDENTITY = 'act';

    public static function stub(): array
    {
        return [
            'act' => 'info', //
            'value' => null, // 'text'
        ];
    }

}