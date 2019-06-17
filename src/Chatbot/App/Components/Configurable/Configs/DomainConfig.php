<?php


namespace Commune\Chatbot\App\Components\Configurable\Configs;


use Commune\Chatbot\Config\Host\MemoryOption;
use Commune\Support\Option;

/**
 * @description 对话系统的可配置模块.
 *
 * @property-read string $domain   对话模块的名称.
 * @property-read string $desc  对话模块的自我介绍.
 * @property-read IntentConfig[] $intents  预定义的意图.
 * @property-read MemoryOption[] $memories 预定义的记忆元
 * @property-read EntityConfig[] $entities 预定义的实体属性
 * @property-read TemplateConfig[] $templates 预定义的对话模板
 */
class DomainConfig extends Option
{
    const IDENTITY = 'domain';

    protected static $associations = [
        'intents[]' => IntentConfig::class,
        'memories[]' => MemoryOption::class,
        'entities[]' => EntityConfig::class,
        'templates[]' => TemplateConfig::class,
    ];

    public static function stub(): array
    {
        return [
            'domain' => '',
            'desc' => '没有填写说明',
            'intents' => [
                // IntentConfig::stub(),
            ],
            'memories' => [
                // MemoryOption::stub(),
            ],
            'entities' => [
                // EntityConfig::stub(),
            ],
            'templates' => [

            ],
        ];
    }

    public function validate(array $data): ? string
    {
        if (empty($data['domain'])) {
            return 'domain should not be empty';
        }

        return null;
    }

    /**
     * @var TemplateConfig[]
     */
    protected $tempsMap;

    public function getTemplatesMap() :array
    {
        if (isset($this->tempsMap)) {
            return $this->tempsMap;
        }
        $this->tempsMap = [];
        foreach ($this->templates as $template) {
            $this->tempsMap[$template->name] = $template->actions;
        }

        return $this->tempsMap;
    }
    /**
     * @return EntityConfig[]
     */
    protected $entitiesMap;

    /**
     * @return EntityConfig[]
     */
    public function getEntitiesMap() : array
    {
        if (isset($this->entitiesMap)) {
            return $this->entitiesMap;
        }
        $this->entitiesMap = [];
        foreach ($this->entities as $entity) {
            $this->entitiesMap[$entity->name] = $entity;
        }

        return $this->entitiesMap;
    }
}