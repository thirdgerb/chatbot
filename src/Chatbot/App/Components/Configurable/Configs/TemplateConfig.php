<?php

namespace Commune\Chatbot\App\Components\Configurable\Configs;

use Commune\Support\Option;


/**
 * @property-read string $name
 * @property-read ActionConfig[] $actions
 */
class TemplateConfig extends Option
{
    protected static $associations = [
        'actions[]' => ActionConfig::class
    ];


    public static function stub(): array
    {
        return [
            'name' => '',
            'actions' => [
                ActionConfig::stub(),
            ]
        ];
    }

    public function getId()
    {
        return $this->name;
    }

}