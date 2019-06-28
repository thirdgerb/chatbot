<?php


namespace Commune\Chatbot\App\Components\Rasa\Options;


use Commune\Support\Option;

/**
 * @property-read string $name
 * @property-read string[] $patterns
 */
class RegexOption extends Option
{
    public static function stub(): array
    {
        return [
            'name' => 'zipcode',
            'patterns' => [
                '[0-9]{5}',
            ]
        ];
    }


}