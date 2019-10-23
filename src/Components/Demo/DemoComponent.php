<?php


namespace Commune\Components\Demo;


use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Chatbot\OOHost\NLU\Options\SynonymOption;
use Commune\Components\Predefined\PredefinedComponent;
use Commune\Components\SimpleChat\SimpleChatComponent;
use Commune\Components\SimpleWiki\SimpleWikiComponent;
use Commune\Components\Story\StoryComponent;

/**
 * @property-read string $langPath 翻译文件所在目录. 为空表示不加载
 * @property-read string $intentsPath 意图语料库所在目录. 为空表示不加载
 * @property-read string $entitiesPath  实体词典所在目录, 为空表示不加载.
 * @property-read string $synonymsPath  同义词词典所在目录, 为空表示不加载.
 */
class DemoComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'langPath' => realpath(__DIR__ .'/resources/langs'),
            'intentsPath' => realpath(__DIR__ .'/resources/nlu/intents.yml'),
            'entitiesPath' => realpath(__DIR__ .'/resources/nlu/entities.yml'),
            'synonymsPath' => realpath(__DIR__ .'/resources/nlu/synonyms.yml'),
        ];
    }

    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Components\\Demo\\",
            __DIR__
        );

        $path = $this->langPath;
        if (!empty($path)) {
            $this->loadTranslationResource($path, Translator::FORMAT_PHP);
        }

        $path = $this->intentsPath;
        if (!empty($path)) {
            $this->registerCorpusOptionFromYaml(
                $path,
                IntentCorpusOption::class
            );
        }

        $path = $this->entitiesPath;
        if (!empty($path)) {
            $this->registerCorpusOptionFromYaml(
                $path,
                EntityDictOption::class
            );
        }

        $path = $this->synonymsPath;
        if (!empty($path)) {
            $this->registerCorpusOptionFromYaml(
                $path,
                SynonymOption::class
            );
        }


        // demo 依赖的组件。
        $this->dependComponent(StoryComponent::class);
        $this->dependComponent(PredefinedComponent::class);
        $this->dependComponent(SimpleChatComponent::class);
        $this->dependComponent(SimpleWikiComponent::class);
    }




}