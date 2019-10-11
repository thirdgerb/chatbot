<?php


namespace Commune\Chatbot\App\Components\Rasa\Options;


use Commune\Support\Option;

/**
 * @property-read string $name
 * @property-read string[] $list
 */
class LookupOption extends Option
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => 'currencies',
            'list' => ['Yen', 'USD', 'RMB']
        ];
    }


}