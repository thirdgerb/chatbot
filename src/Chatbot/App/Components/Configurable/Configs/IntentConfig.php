<?php


namespace Commune\Chatbot\App\Components\Configurable\Configs;


use Commune\Chatbot\OOHost\Context\Intent\IntentMatcherOption;
use Commune\Support\Option;

/**
 * @property-read string $name intent的名字. 通常在某个domain之下.
 * @property-read string $desc intent的简介.
 * @property-read IntentMatcherOption $matcher
 * @property-read string[] $entities 为intent定义的entity, 分享domain中的定义.
 * @property-read ActionConfig[] $actions
 */
class IntentConfig extends Option
{
    protected static $associations = [
        'matcher' => IntentMatcherOption::class,
        'actions[]' => ActionConfig::class,
    ];

    public static function stub(): array
    {
        return [
            'name' => '',
            'desc' => '',
            'matcher' => IntentMatcherOption::stub(),
            'actions' => ActionConfig::stub(),
        ];
    }

    public function validate(array $data): ? string
    {
        if (empty($data['name'])) {
            return 'name can not be empty';
        }
        return null;
    }

    public function getId()
    {
        return $this->name;
    }


}