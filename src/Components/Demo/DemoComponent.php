<?php


namespace Commune\Components\Demo;


use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\SimpleChat\SimpleChatComponent;
use Commune\Components\Story\StoryComponent;

/**
 * @property-read string $langPath
 * @property-read string $langLoader
 */
class DemoComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'langPath' => realpath(__DIR__ .'/resources/langs'),
            'langLoader' => Translator::FORMAT_PHP,
        ];
    }

    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Components\\Demo\\",
            __DIR__
        );

        $this->loadTranslationResource($this->langPath, $this->langLoader);

        $this->dependComponent(StoryComponent::class);
        $this->dependComponent(SimpleChatComponent::class);
    }




}