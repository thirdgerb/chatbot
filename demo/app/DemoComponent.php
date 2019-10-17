<?php


namespace Commune\Demo\App;


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
    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Demo\\App\\",
            __DIR__
        );

        $this->loadTranslationResource($this->langPath, $this->langLoader);

        $this->dependComponent(StoryComponent::class);
        $this->dependComponent(SimpleChatComponent::class);
    }



    public static function stub(): array
    {
        return [
            'langPath' => realpath(__DIR__ .'/../langs'),
            'langLoader' => Translator::FORMAT_PHP,
        ];
    }

}