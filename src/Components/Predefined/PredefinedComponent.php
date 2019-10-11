<?php


namespace Commune\Components\Predefined;


use Commune\Chatbot\Framework\Component\ComponentOption;

/**
 * 系统预定义的组件. 提供demo样例, navigator等.
 */
class PredefinedComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [];
    }

    protected function doBootstrap(): void
    {
        $namespace = "Commune\\Components\\Predefined\\";
        $path = __DIR__  ;

        $this->loadSelfRegisterByPsr4(
            $namespace,
            $path
        );

        $this->loadIntentCorpusFromYaml(__DIR__ .'/resources/intents.yaml');
    }



}