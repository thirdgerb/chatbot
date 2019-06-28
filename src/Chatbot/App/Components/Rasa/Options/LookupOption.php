<?php


namespace Commune\Chatbot\App\Components\Rasa\Options;


use Commune\Support\Option;

/**
 * @property-read string $name
 * @property-read string[] $list
 */
class LookupOption extends Option
{
    public static function stub(): array
    {
        return [
            'name' => 'currencies',
            'list' => ['Yen', 'USD', 'RMB']
        ];
    }

    public function getId()
    {
        return $this->name;
    }

}