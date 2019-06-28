<?php


namespace Commune\Chatbot\App\Components\Rasa\Options;


use Commune\Support\Option;

/**
 * @property-read string $name
 * @property-read string[] $words
 */
class SynonymOption extends Option
{
    public static function stub(): array
    {
        return [
            'name' => 'type',
            'words' => [ 'word1', 'word2'],
        ];
    }

    public function getId()
    {
        return $this->name;
    }

}