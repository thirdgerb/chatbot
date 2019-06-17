<?php


namespace Commune\Chatbot\App\Components\Configurable\Configs;


use Commune\Support\Option;

/**
 * @property-read string $name
 * @property-read string $question
 * @property-read string $memoryName 为空字符串时表示不是memory
 * @property-read string $memoryKey 为空字符串时表示使用
 */
class EntityConfig extends Option
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'question' => '',
            'memoryName' => '',
            'memoryKey' => '',
        ];
    }

    public function getId()
    {
        return $this->name;
    }

}