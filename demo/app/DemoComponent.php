<?php


namespace Commune\Demo\App;


use Commune\Chatbot\App\Components\SelfRegisterComponent;
use Commune\Chatbot\Contracts\Translator;

/**
 * @property-read string $langPath
 * @property-read string $langLoader
 */
class DemoComponent extends SelfRegisterComponent
{
    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Demo\\App\\",
            __DIR__
        );

        $this->loadTranslationResource($this->langPath, $this->langLoader);
    }



    public static function stub(): array
    {
        return [
            'langPath' => __DIR__ .'/../langs',
            'langLoader' => Translator::FORMAT_PHP,
        ];
    }

}